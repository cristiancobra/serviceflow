<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\DateTimeConversionService;

class TransactionsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $timezone = auth()->user()->timezone ?? 'America/Sao_Paulo';

        return [
            'id' => $this->id,
            'invoice_id' => $this->invoice_id,
            'bank_account_id' => $this->bank_account_id,
            'payment_batch_id' => $this->payment_batch_id,
            'amount' => $this->amount,
            'transaction_date' => DateTimeConversionService::convertFromUtc(
                $this->transaction_date,
                $timezone
            ),
            'type' => $this->type,
            'method' => $this->method,
            // Cartão em que o pagamento foi lançado (quando method = credit_card)
            'credit_card_id' => $this->when(
                $this->relationLoaded('creditCardCharge'),
                fn () => $this->creditCardCharge?->credit_card_id
            ),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'invoice' => new InvoicesResource($this->whenLoaded('invoice')),
            'payment_batch' => $this->when($this->relationLoaded('paymentBatch') && $this->paymentBatch, function () {
                return [
                    'id' => $this->paymentBatch->id,
                    'amount' => $this->paymentBatch->amount,
                    'description' => $this->paymentBatch->description,
                ];
            }),
            'bank_account' => $this->when($this->relationLoaded('bankAccount') && $this->bankAccount, function () {
                return [
                    'id' => $this->bankAccount->id,
                    'name' => $this->bankAccount->account_name ?? 'N/A',
                    'account_name' => $this->bankAccount->account_name ?? 'N/A',
                ];
            }),
        ];
    }
}