<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BankAccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'account_id' => $this->account_id,
            'user_id' => $this->user_id,
            'account_name' => $this->account_name,
            'account_number' => $this->account_number,
            'bank_name' => $this->bank_name,
            'agency' => $this->agency,
            'initial_balance' => $this->initial_balance,
            'initial_balance_formatted' => 'R$ ' . number_format($this->initial_balance, 2, ',', '.'),
            'type' => $this->type,
            'type_label' => $this->type_label,
            'is_active' => $this->is_active,
            'description' => $this->description,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            
            // Relacionamentos
            'account' => new AccountResource($this->whenLoaded('account')),
            'user' => $this->when($this->relationLoaded('user'), function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
            'transactions_count' => $this->when(isset($this->transactions_count), $this->transactions_count),
        ];
    }
}
