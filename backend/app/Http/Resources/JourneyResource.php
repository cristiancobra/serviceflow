<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\DateTimeConversionService;
use App\Http\Resources\TasksResource;

class JourneyResource extends JsonResource
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
            'name' => $this->name,
            'user_id' => $this->user_id,
            'task_id' => $this->task_id,
            'details' => $this->details,
            'start' => DateTimeConversionService::convertFromUtc(
                $this->start,
                $timezone
            ),
            'end' => $this->end ? DateTimeConversionService::convertFromUtc(
                $this->end,
                $timezone
            ) : null,
            'duration' => $this->duration,
            'task' => $this->when($this->task, function () {
                return new TasksResource($this->task);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
