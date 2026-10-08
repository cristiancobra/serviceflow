<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\LeadsResource;
use App\Http\Resources\LinksResource;
use App\Http\Resources\UsersResource;

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
        return [
            'id' => $this->id,
            'account_id' => $this->account_id,
            'lead_id' => $this->lead_id,
            'user_id' => $this->user_id,
            'company_id' => $this->company_id,
            'name' => $this->name,
            'category' => $this->category,
            // Colunas date (sem hora): vão como Y-m-d, sem conversão de fuso
            'date_start' => $this->date_start,
            'date_due' => $this->date_due,
            'date_conclusion' => $this->date_conclusion,
            'date_canceled' => $this->date_canceled,
            'description' => $this->description,
            'duration_time' => $this->duration_time,
            'contracted_hours' => $this->whenLoaded('proposals', fn () => $this->contractedHours()),
            'duration_by_department' => $this->whenLoaded('tasks', fn () => $this->durationByDepartment()),
            'source' => $this->source,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Relationships
            'journeys' => JourneyResource::collection($this->whenLoaded('journeys')),
            // 'project' => new ProjectResource($this->whenLoaded('project')),
            'company' => new CompanyResource($this->whenLoaded('company')),
            'lead' => new LeadsResource($this->whenLoaded('lead')),
            'user' => new UsersResource($this->whenLoaded('user')),
            'links' => LinksResource::collection($this->whenLoaded('links')),
            'tasks' => TasksResource::collection($this->whenLoaded('tasks')),
            'proposals' => ProposalsResource::collection($this->whenLoaded('proposals')),
            'due_date_changes' => DueDateChangeResource::collection($this->whenLoaded('dueDateChanges')),
		];
    }

    /**
     * Horas contratadas (em segundos): total_hours da proposta aceita mais recente,
     * mesma regra do relatório de horas. Sem proposta aceita = 0.
     */
    private function contractedHours()
    {
        $proposal = $this->proposals
            ->where('status', 'accepted')
            ->sortByDesc('accepted_at')
            ->first();

        return (int) ($proposal->total_hours ?? 0);
    }

    /**
     * Soma o duration_time das tarefas agrupado por departamento.
     * Tarefas sem departamento ficam num grupo com department = null.
     */
    private function durationByDepartment()
    {
        return $this->tasks
            ->groupBy(fn ($task) => $task->department_id ?? 0)
            ->map(function ($tasks) {
                $department = $tasks->first()->department;

                return [
                    'department' => $department ? [
                        'id' => $department->id,
                        'name' => $department->name,
                        'color' => $department->color,
                        'icon' => $department->icon,
                    ] : null,
                    'duration_time' => (int) $tasks->sum('duration_time'),
                ];
            })
            ->filter(fn ($group) => $group['duration_time'] > 0)
            ->sortByDesc('duration_time')
            ->values();
    }
}