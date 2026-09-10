<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CreditCardChargeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'credit_card_id' => $this->credit_card_id,
            'credit_card_invoice_id' => $this->credit_card_invoice_id,
            'description' => $this->description,
            'amount' => $this->amount,
            'amount_formatted' => 'R$ ' . number_format($this->amount, 2, ',', '.'),
            'purchase_date' => $this->purchase_date?->format('Y-m-d'),
            'category' => $this->category,
            'installment_number' => $this->installment_number,
            'installment_total' => $this->installment_total,
            'installment_label' => "{$this->installment_number}/{$this->installment_total}",
            'installment_group_id' => $this->installment_group_id,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
