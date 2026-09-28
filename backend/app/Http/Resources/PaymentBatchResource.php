<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\DateTimeConversionService;

class PaymentBatchResource extends JsonResource
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
            'bank_account_id' => $this->bank_account_id,
            'amount' => $this->amount,
            'transaction_date' => DateTimeConversionService::convertFromUtc(
                $this->transaction_date,
                $timezone
            ),
            'type' => $this->type,
            'method' => $this->method,
            'description' => $this->description,
            'transactions' => TransactionsResource::collection($this->whenLoaded('transactions')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
