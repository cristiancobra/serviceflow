<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
        return [
            'id' => $this->id,
            'invoice_id' => $this->invoice_id,
            'bank_account_id' => $this->bank_account_id,
            'amount' => $this->amount,
            'transaction_date' => $this->transaction_date,
            'type' => $this->type,
            'method' => $this->method,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'invoice' => new InvoicesResource($this->whenLoaded('invoice')),
            'bank_account' => $this->when($this->relationLoaded('bankAccount'), function () {
                return [
                    'id' => $this->bankAccount->id,
                    'name' => $this->bankAccount->name ?? 'N/A',
                ];
            }),
        ];
    }
}