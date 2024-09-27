<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\OpportunityResource;
use App\Http\Resources\ProposalItemResource;

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
            'total_profit_percentage' => $this->total_profit_percentage,
            'total_profit' => $this->total_profit,
            'total_discount' => $this->total_discount,
            'total_price' => $this->total_price,

             // Relationships
             'proposalItems' => ProposalItemResource::collection($this->whenLoaded('proposalItems')),
            'opportunity' => new OpportunityResource($this->whenLoaded('opportunity')),
        ];
    }
}
