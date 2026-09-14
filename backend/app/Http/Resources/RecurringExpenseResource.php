<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RecurringExpenseResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'account_id' => $this->account_id,
            'user_id' => $this->user_id,
            'department_id' => $this->department_id,
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->category,
            'amount' => $this->amount,
            'amount_formatted' => 'R$ ' . number_format($this->amount, 2, ',', '.'),
            'due_day' => $this->due_day,
            'is_active' => $this->is_active,
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),

            'user' => $this->when($this->relationLoaded('user') && $this->user, function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                ];
            }),
            'department' => $this->when($this->relationLoaded('department') && $this->department, function () {
                return [
                    'id' => $this->department->id,
                    'name' => $this->department->name,
                ];
            }),
            'invoices_count' => $this->when(isset($this->invoices_count), $this->invoices_count),
            'invoices' => InvoicesResource::collection($this->whenLoaded('invoices')),
        ];
    }
}
