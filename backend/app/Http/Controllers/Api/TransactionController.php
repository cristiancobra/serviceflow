<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionRequest;
use App\Models\CreditCard;
use App\Models\CreditCardCharge;
use App\Models\CreditCardInvoice;
use App\Models\Transaction;
use App\Http\Resources\TransactionsResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transactions = Transaction::with([
            'invoice.proposal.opportunity.company',
            'invoice.proposal.opportunity.lead',
            'invoice.company',
            'invoice.lead',
            'bankAccount'
        ])
            ->orderBy('transaction_date', 'desc')
            ->paginate(500);

        return TransactionsResource::collection($transactions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  TransactionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TransactionRequest $request)
    {
        try {
            $validated = $request->validated();
            $creditCardId = $validated['credit_card_id'] ?? null;
            unset($validated['credit_card_id']);

            $transaction = Transaction::create($validated);

            // Atualiza o total_paid e status da invoice
            if ($transaction->invoice) {
                $transaction->invoice->updateTotalPaid();
                $transaction->invoice->updateStatus();
            }

            // Pagamento feito no cartão de crédito: lança a compra na fatura do cartão
            if ($transaction->method === 'credit_card' && $creditCardId) {
                $this->attachCreditCardCharge($transaction, $creditCardId);
            }

            return TransactionsResource::make($transaction->load('invoice', 'bankAccount'));

        } catch (\Exception $e) {
            return response()->json([
                'message' => "Erro interno do servidor",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Lança, na fatura aberta do cartão, a compra correspondente a um pagamento
     * de invoice feito com o método "cartão de crédito", e liga as duas.
     */
    private function attachCreditCardCharge(Transaction $transaction, int $creditCardId)
    {
        $creditCard = CreditCard::findOrFail($creditCardId);
        $invoice = $transaction->invoice;

        $cardInvoice = $creditCard->invoiceForPurchaseDate(
            Carbon::parse($transaction->transaction_date)
        );

        $charge = CreditCardCharge::create([
            'account_id' => $creditCard->account_id,
            'credit_card_id' => $creditCard->id,
            'credit_card_invoice_id' => $cardInvoice->id,
            'description' => $invoice?->name ?: "Fatura #{$transaction->invoice_id}",
            'amount' => $transaction->amount,
            'purchase_date' => $transaction->transaction_date,
        ]);

        $transaction->update(['credit_card_charge_id' => $charge->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  TransactionRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TransactionRequest $request, $id)
    {
        $validated = $request->validated();
        unset($validated['credit_card_id']);

        $transaction = Transaction::findOrFail($id);

        $transaction->fill($validated);
        $transaction->save();

        // Atualiza o total_paid e status da invoice
        if ($transaction->invoice) {
            $transaction->invoice->updateTotalPaid();
            $transaction->invoice->updateStatus();
        }

        // Mantém a compra lançada no cartão (se houver) com o mesmo valor/data do pagamento
        if ($transaction->creditCardCharge) {
            $transaction->creditCardCharge->update([
                'amount' => $transaction->amount,
                'purchase_date' => $transaction->transaction_date,
            ]);
        }

        return TransactionsResource::make($transaction->load('invoice', 'bankAccount'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $transaction = Transaction::findOrFail($id);

            if ($transaction->creditCardCharge
                && $transaction->creditCardCharge->creditCardInvoice->status !== CreditCardInvoice::STATUS_OPEN) {
                return response()->json([
                    'message' => 'Não é possível excluir um pagamento cujo lançamento no cartão já está em uma fatura fechada.'
                ], 422);
            }

            // O soft delete vai disparar o evento 'deleted' no modelo
            // que automaticamente atualizará o total_paid e status da invoice
            $transaction->delete();

            // Remove também a compra lançada no cartão, se houver
            $transaction->creditCardCharge?->delete();

            return response()->json([
                'message' => 'Transação excluída com sucesso',
                'data' => null
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao excluir transação',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get report of transactions by year
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function report(Request $request)
    {
        $year = $request->query('year', date('Y'));

        // Busca transações do ano filtrado
        $transactions = Transaction::whereYear('transaction_date', $year)->get();

        // Calcula total de entradas (crédito)
        $totalEntries = $transactions->where('type', 'credit')->sum('amount');

        // Calcula total de saídas (débito)
        $totalExits = $transactions->where('type', 'debit')->sum('amount');

        // Calcula o saldo
        $balance = $totalEntries - $totalExits;

        return response()->json([
            'totalEntries' => $totalEntries,
            'totalExits' => $totalExits,
            'balance' => $balance,
            'year' => $year,
        ]);
    }
}
