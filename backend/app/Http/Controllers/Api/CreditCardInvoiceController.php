<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreditCardInvoicePaymentRequest;
use App\Http\Resources\CreditCardInvoiceResource;
use App\Models\CreditCardInvoice;
use App\Models\Transaction;
use Illuminate\Http\Request;

class CreditCardInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = CreditCardInvoice::with('creditCard');

        if ($request->has('credit_card_id')) {
            $query->where('credit_card_id', $request->credit_card_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $query->orderBy('reference_year', 'desc')->orderBy('reference_month', 'desc');

        $perPage = $request->get('per_page', 24);
        $invoices = $query->paginate($perPage);

        return CreditCardInvoiceResource::collection($invoices);
    }

    public function show(CreditCardInvoice $creditCardInvoice)
    {
        $creditCardInvoice->load([
            'creditCard',
            'charges' => fn ($q) => $q->orderBy('purchase_date', 'asc'),
            'transactions.bankAccount',
        ]);

        return new CreditCardInvoiceResource($creditCardInvoice);
    }

    /**
     * Fecha manualmente a fatura (open -> closed), tornando-a elegível para pagamento.
     */
    public function close(CreditCardInvoice $creditCardInvoice)
    {
        if ($creditCardInvoice->status !== CreditCardInvoice::STATUS_OPEN) {
            return response()->json([
                'message' => 'Esta fatura já foi fechada.'
            ], 422);
        }

        $creditCardInvoice->update(['status' => CreditCardInvoice::STATUS_CLOSED]);
        $creditCardInvoice->updateStatus();

        return new CreditCardInvoiceResource($creditCardInvoice->fresh(['creditCard']));
    }

    /**
     * Registra o pagamento (total ou parcial) da fatura: cria uma Transaction
     * de débito saindo da conta bancária informada. É a única transação real
     * entre contas em todo o ciclo do cartão.
     */
    public function pay(CreditCardInvoicePaymentRequest $request, CreditCardInvoice $creditCardInvoice)
    {
        if ($creditCardInvoice->status === CreditCardInvoice::STATUS_OPEN) {
            return response()->json([
                'message' => 'Feche a fatura antes de registrar o pagamento.'
            ], 422);
        }

        $data = $request->validated();

        $transaction = new Transaction([
            'credit_card_invoice_id' => $creditCardInvoice->id,
            'bank_account_id' => $data['bank_account_id'],
            'amount' => $data['amount'],
            'transaction_date' => $data['transaction_date'],
            'type' => 'debit',
            'method' => $data['method'],
        ]);
        $transaction->account_id = $creditCardInvoice->account_id;
        $transaction->save();

        return new CreditCardInvoiceResource($creditCardInvoice->fresh(['creditCard', 'charges', 'transactions.bankAccount']));
    }
}
