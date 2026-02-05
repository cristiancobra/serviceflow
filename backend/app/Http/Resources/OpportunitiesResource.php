<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\LeadsResource;
use App\Http\Resources\LinksResource;
use App\Services\DateTimeConversionService;

class OpportunitiesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $timezone = auth()->user()->timezone ?? 'America/Sao_Paulo';

        return [
            'id' => $this->id,
            'account_id' => $this->account_id,
            'lead_id' => $this->lead_id,
            'user_id' => $this->user_id,
            'company_id' => $this->company_id,
            'name' => $this->name,
            'category' => $this->category,
            'date_start' => DateTimeConversionService::convertFromUtc($this->date_start, $timezone),
            'date_due' => DateTimeConversionService::convertFromUtc($this->date_due, $timezone),
            'date_conclusion' => DateTimeConversionService::convertFromUtc($this->date_conclusion, $timezone),
            'date_canceled' => DateTimeConversionService::convertFromUtc($this->date_canceled, $timezone),
            'description' => $this->description,
            'duration_time' => $this->duration_time,
            'source' => $this->source,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Relationships
            'journeys' => JourneyResource::collection($this->whenLoaded('journeys')),
            // 'project' => new ProjectResource($this->whenLoaded('project')),
            'company' => new CompanyResource($this->whenLoaded('company')),
            'lead' => new LeadsResource($this->whenLoaded('lead')),
            'links' => LinksResource::collection($this->whenLoaded('links')),
            'tasks' => TasksResource::collection($this->whenLoaded('tasks')),
            'proposals' => ProposalsResource::collection($this->whenLoaded('proposals')),
			];
    }
}