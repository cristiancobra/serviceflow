<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OpportunityResource extends JsonResource
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
            'lead_id' => $this->lead_id,
            'user_id' => $this->user_id,
            'company_id' => $this->company_id,
            'name' => $this->name,
            'category' => $this->category,
            'date_start' => $this->date_start,
            'date_due' => $this->date_due,
            'date_conclusion' => $this->date_conclusion,
            'description' => $this->description,
            'duration_time' => $this->duration_time,
            'source' => $this->source,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Relationships
            // 'journeys' => JourneyResource::collection($this->whenLoaded('journeys')),
            // 'project' => new ProjectResource($this->whenLoaded('project')),
            'company' => new CompanyResource($this->whenLoaded('company')),
            'lead' => new LeadResource($this->whenLoaded('lead')),
			];
    }
}