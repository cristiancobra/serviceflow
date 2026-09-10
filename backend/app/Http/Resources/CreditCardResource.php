<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CreditCardResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'account_id' => $this->account_id,
            'user_id' => $this->user_id,
            'default_bank_account_id' => $this->default_bank_account_id,
            'name' => $this->name,
            'brand' => $this->brand,
            'last_digits' => $this->last_digits,
            'credit_limit' => $this->credit_limit,
            'credit_limit_formatted' => 'R$ ' . number_format($this->credit_limit, 2, ',', '.'),
            'closing_day' => $this->closing_day,
            'due_day' => $this->due_day,
            'is_active' => $this->is_active,
            'description' => $this->description,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),

            'user' => $this->when($this->relationLoaded('user'), function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
            'default_bank_account' => $this->when($this->relationLoaded('defaultBankAccount') && $this->defaultBankAccount, function () {
                return [
                    'id' => $this->defaultBankAccount->id,
                    'account_name' => $this->defaultBankAccount->account_name,
                ];
            }),
            'invoices_count' => $this->when(isset($this->invoices_count), $this->invoices_count),
        ];
    }
}
