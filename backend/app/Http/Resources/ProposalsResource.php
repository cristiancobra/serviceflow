<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\InvoicesResource;
use App\Http\Resources\OpportunityResource;
use App\Http\Resources\ProposalServiceResource;
use App\Http\Resources\ProposalCostResource;

class ProposalsResource extends JsonResource
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
            'installment_quantity' => $this->installment_quantity,
            'draft_at' => $this->draft_at,
            'submitted_at' => $this->submitted_at,
            'approved_at' => $this->approved_at,
            'rejected_at' => $this->rejected_at,
            'canceled_at' => $this->canceled_at,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            

            // Relationships
            'invoices' => InvoicesResource::collection($this->whenLoaded('invoices')),
            'proposalServices' => ProposalServiceResource::collection($this->whenLoaded('proposalServices')),
            'proposalCosts' => ProposalCostResource::collection($this->whenLoaded('proposalCosts')),
            'opportunity' => new OpportunityResource($this->whenLoaded('opportunity')),
        ];
    }
}
