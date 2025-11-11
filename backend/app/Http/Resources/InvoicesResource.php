<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\OpportunitiesResource;
use App\Http\Resources\ProposalsResource;
use App\Http\Resources\TransactionsResource;
use App\Http\Resources\UsersResource;

class InvoicesResource extends JsonResource
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
            'proposal_id' => $this->proposal_id,
            'user_id' => $this->user_id,
            'date_due' => $this->date_due,
            'price' => $this->price,
            'total_paid' => $this->total_paid,
            'status' => $this->status,
            'description' => $this->description,
            'invoice_number' => $this->invoice_number,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'opportunity' => new OpportunitiesResource($this->whenLoaded('proposal.opportunity')),
            'proposal' => new ProposalsResource($this->whenLoaded('proposal')),
            'transactions' => TransactionsResource::collection($this->whenLoaded('transactions')),
            'user' => new UsersResource($this->whenLoaded('user')),
        ];
    }
}