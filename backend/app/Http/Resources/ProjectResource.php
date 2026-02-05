<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\TasksResource;
use App\Services\DateTimeConversionService;

class ProjectResource extends JsonResource
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
			"id" => $this->id,
			"account_id" => $this->account_id,
			"opportunity_id" => $this->opportunity_id,
			"user_id" => $this->user_id,
			"contact_id" => $this->contact_id,
			"company_id" => $this->company_id,
			"goal_id" => $this->goal_id,
			"name" => $this->name,
			"category" => $this->category,
			"budget" => $this->budget,
			"actual_cost" => $this->actual_cost,
			"date_start" => DateTimeConversionService::convertFromUtc($this->date_start, $timezone),
			"date_due" => DateTimeConversionService::convertFromUtc($this->date_due, $timezone),
			"date_conclusion" => DateTimeConversionService::convertFromUtc($this->date_conclusion, $timezone),
			"description" => $this->description,
			"priority" => $this->priority,
			"status" => $this->status,
			"type" => $this->type,
			"created_at" => $this->created_at,
			"updated_at" => $this->updated_at,

			// relationships
			"tasks" => TasksResource::collection($this->whenLoaded('tasks')), // Adiciona o relacionamento tasks
			];
    }
}
