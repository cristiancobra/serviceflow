<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProposalServiceResource extends JsonResource
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
            'proposal_id' => $this->proposal_id,
            'service_id' => $this->service_id,
            'account_id' => $this->account_id,
            'name' => $this->name,
            'quantity' => $this->quantity,
            'category' => $this->category,
            'labor_hours' => $this->labor_hours,
            'labor_hours_total' => $this->labor_hours_total,
            'labor_hourly_rate' => $this->labor_hourly_rate,
            'labor_hourly_rate_total' => $this->labor_hourly_rate_total,
            'profit_percentage' => $this->profit_percentage_total,
            'profit' => $this->profit,
            'price' => $this->price,
            'total_profit' => $this->total_profit,
            'total_price' => $this->total_price,
            'observations' => $this->observations,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'deleted_at' => $this->deleted_at ? $this->deleted_at->toDateTimeString() : null,
        ];
    }
}
