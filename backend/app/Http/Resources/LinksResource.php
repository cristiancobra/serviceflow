<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\TasksResource;
use App\Http\Resources\OpportunitiesResource;

class LinksResource extends JsonResource
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
            'user_id' => $this->user_id,
            'task_id' => $this->task_id,
            'opportunity_id' => $this->opportunity_id,
            'url' => $this->url,
            'title' => $this->title,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            //relationships
            'opportunity' => new OpportunitiesResource($this->whenLoaded('opportunity')),
            'task' => new TasksResource($this->whenLoaded('task')),
        ];
    }
}
