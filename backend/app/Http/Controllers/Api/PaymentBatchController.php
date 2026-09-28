<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentBatchRequest;
use App\Http\Resources\PaymentBatchResource;
use App\Models\Invoice;
use App\Models\PaymentBatch;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Pagamento de várias invoices em uma única movimentação bancária. Cria o lote
 * e uma transaction por invoice ligada a ele, tudo ou nada.
 */
class PaymentBatchController extends Controller
{
    public function store(PaymentBatchRequest $request)
    {
        $validated = $request->validated();

        $invoices = Invoice::whereIn('id', collect($validated['items'])->pluck('invoice_id'))
            ->get()
            ->keyBy('id');

        if ($invoices->count() !== count($validated['items'])) {
            throw ValidationException::withMessages([
                'items' => 'Uma das faturas selecionadas não foi encontrada.',
            ]);
        }

        // Todas precisam ser do mesmo tipo: uma movimentação é ou entrada ou saída
        $types = $invoices->pluck('type')->unique();
        if ($types->count() !== 1) {
            throw ValidationException::withMessages([
                'items' => 'Não é possível pagar contas a pagar e a receber no mesmo lote.',
            ]);
        }

        if ($invoices->contains('status', Invoice::STATUS_CANCELLED)) {
            throw ValidationException::withMessages([
                'items' => 'Uma das faturas selecionadas está cancelada.',
            ]);
        }

        $type = $types->first() === 'debit' ? 'debit' : 'credit';

        $batch = DB::transaction(function () use ($validated, $invoices, $type) {
            $batch = PaymentBatch::create([
                'bank_account_id' => $validated['bank_account_id'],
                'amount' => collect($validated['items'])->sum('amount'),
                'transaction_date' => $validated['transaction_date'],
                'type' => $type,
                'method' => $validated['method'],
                'description' => $validated['description'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                Transaction::create([
                    'invoice_id' => $item['invoice_id'],
                    'payment_batch_id' => $batch->id,
                    'bank_account_id' => $batch->bank_account_id,
                    'amount' => $item['amount'],
                    'transaction_date' => $batch->transaction_date,
                    'type' => $type,
                    'method' => $batch->method,
                ]);

                $invoices[$item['invoice_id']]->refresh()->updateStatus();
            }

            return $batch;
        });

        return PaymentBatchResource::make($batch->fresh()->load('transactions.invoice'));
    }

    public function show(PaymentBatch $paymentBatch)
    {
        return PaymentBatchResource::make($paymentBatch->load('transactions.invoice', 'bankAccount'));
    }

    /**
     * Estorna o lote inteiro: exclui todas as suas transactions (o que reabre
     * o saldo de cada invoice) e o próprio lote.
     */
    public function destroy(PaymentBatch $paymentBatch)
    {
        DB::transaction(function () use ($paymentBatch) {
            foreach ($paymentBatch->transactions as $transaction) {
                $invoice = $transaction->invoice;
                $transaction->delete();
                $invoice?->refresh()->updateStatus();
            }

            // Normalmente já foi excluído pelo updateAmount() da última transaction
            PaymentBatch::whereKey($paymentBatch->id)->first()?->delete();
        });

        return response()->json([
            'message' => 'Pagamento em lote estornado com sucesso',
            'data' => null,
        ], 200);
    }
}
