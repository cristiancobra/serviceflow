<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProposalResource extends JsonResource
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
            'opportunity_id' => $this->opportunity_id,
            'name' => $this->name,
            'description' => $this->description,
            'date' => $this->date,
            'validity_days' => $this->validity_days,
            'total_hours' => $this->total_hours,
            'total_operational_cost' => $this->total_operational_cost,
            'total_third_party_cost' => $this->total_third_party_cost,
            'profit_margin' => $this->profit_margin,
            'total_discount' => $this->total_discount,
            'total_price' => $this->total_price,
        ];
    }
}
