<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TasksResource extends JsonResource
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
			"id" => $this->id,
			"account_id" => $this->account_id,
			"user_id" => $this->user_id,
			"contact_id" => $this->contact_id,
			"company_id" => $this->company_id,
			"goal_id" => $this->goal_id,
			"project_id" => $this->project_id,
			"name" => $this->name,
			"category" => $this->category,
			"date_start" => $this->date_start,
			"date_due" => $this->date_due,
			"date_conclusion" => $this->date_conclusion,
			"duration_days" => $this->duration_days,
			'duration_time' => $this->duration_time,
			"description" => $this->description,
			"priority" => $this->priority,
			"status" => $this->status,
			"created_at" => $this->created_at,
			"updated_at" => $this->updated_at,
			];
    }
}        