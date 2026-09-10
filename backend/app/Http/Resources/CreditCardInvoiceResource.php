<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CreditCardInvoiceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'credit_card_id' => $this->credit_card_id,
            'reference_month' => $this->reference_month,
            'reference_year' => $this->reference_year,
            'reference_label' => $this->reference_label,
            'closing_date' => $this->closing_date?->format('Y-m-d'),
            'due_date' => $this->due_date?->format('Y-m-d'),
            'total_amount' => $this->total_amount,
            'total_amount_formatted' => 'R$ ' . number_format($this->total_amount, 2, ',', '.'),
            'total_paid' => $this->total_paid,
            'total_paid_formatted' => 'R$ ' . number_format($this->total_paid, 2, ',', '.'),
            'balance' => $this->balance,
            'balance_formatted' => 'R$ ' . number_format($this->balance, 2, ',', '.'),
            'status' => $this->status,
            'status_label' => $this->status_label,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),

            'credit_card' => new CreditCardResource($this->whenLoaded('creditCard')),
            'charges' => CreditCardChargeResource::collection($this->whenLoaded('charges')),
            'transactions' => TransactionsResource::collection($this->whenLoaded('transactions')),
        ];
    }
}
