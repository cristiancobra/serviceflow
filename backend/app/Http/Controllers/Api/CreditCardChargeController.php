<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreditCardChargeRequest;
use App\Http\Resources\CreditCardChargeResource;
use App\Models\CreditCard;
use App\Models\CreditCardCharge;
use App\Models\Invoice;
use Illuminate\Support\Str;

class CreditCardChargeController extends Controller
{
    /**
     * Lança uma compra no cartão. Se installment_total > 1, divide o valor em
     * parcelas e cria uma CreditCardCharge em cada fatura correspondente,
     * de acordo com o dia de fechamento do cartão.
     */
    public function store(CreditCardChargeRequest $request)
    {
        $data = $request->validated();

        $creditCard = CreditCard::findOrFail($data['credit_card_id']);
        $installmentTotal = $data['installment_total'] ?? 1;
        $purchaseDate = \Carbon\Carbon::parse($data['purchase_date']);

        $amounts = Invoice::splitIntoInstallments((float) $data['amount'], $installmentTotal);
        $groupId = (string) Str::uuid();

        $charges = [];
        foreach ($amounts as $index => $amount) {
            $referenceDate = $purchaseDate->copy()->addMonthsNoOverflow($index);
            $invoice = $creditCard->invoiceForPurchaseDate($referenceDate);

            $charges[] = CreditCardCharge::create([
                'account_id' => $creditCard->account_id,
                'credit_card_id' => $creditCard->id,
                'credit_card_invoice_id' => $invoice->id,
                'description' => $data['description'],
                'amount' => $amount,
                'purchase_date' => $purchaseDate,
                'category' => $data['category'] ?? null,
                'installment_number' => $index + 1,
                'installment_total' => $installmentTotal,
                'installment_group_id' => $groupId,
            ]);
        }

        return CreditCardChargeResource::collection(collect($charges));
    }

    /**
     * Remove uma parcela avulsa. Só é permitido enquanto a fatura correspondente
     * ainda estiver em aberto (não faz sentido alterar uma fatura já fechada/paga).
     */
    public function destroy(CreditCardCharge $creditCardCharge)
    {
        if ($creditCardCharge->creditCardInvoice->status !== \App\Models\CreditCardInvoice::STATUS_OPEN) {
            return response()->json([
                'message' => 'Não é possível excluir uma compra de uma fatura que já foi fechada.'
            ], 422);
        }

        $creditCardCharge->delete();

        // Se essa compra veio do pagamento de uma invoice no cartão, desfaz o pagamento também
        $creditCardCharge->transaction?->delete();

        return response()->json([
            'message' => 'Compra excluída com sucesso.'
        ]);
    }

    /**
     * Remove todas as parcelas de uma mesma compra (mesmo installment_group_id),
     * desde que nenhuma delas esteja em fatura já fechada.
     */
    public function destroyGroup($installmentGroupId)
    {
        $charges = CreditCardCharge::where('installment_group_id', $installmentGroupId)
            ->with('creditCardInvoice')
            ->get();

        if ($charges->isEmpty()) {
            return response()->json(['message' => 'Compra não encontrada.'], 404);
        }

        $hasClosedInvoice = $charges->contains(
            fn ($charge) => $charge->creditCardInvoice->status !== \App\Models\CreditCardInvoice::STATUS_OPEN
        );

        if ($hasClosedInvoice) {
            return response()->json([
                'message' => 'Não é possível excluir esta compra pois uma ou mais parcelas já estão em fatura fechada.'
            ], 422);
        }

        foreach ($charges as $charge) {
            $charge->delete();
        }

        return response()->json([
            'message' => 'Compra excluída com sucesso.'
        ]);
    }
}
