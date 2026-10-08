<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\DueDateChange;
use App\Services\DateTimeConversionService;

class DueDateChangeResource extends JsonResource
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
            'previous_date' => $this->formatDueDate($this->previous_date, $timezone),
            'new_date' => $this->formatDueDate($this->new_date, $timezone),
            'reason' => $this->reason,
            'reason_label' => DueDateChange::reasonLabel($this->reason),
            'note' => $this->note,
            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
            ]),
            'created_at' => DateTimeConversionService::convertFromUtc($this->created_at, $timezone),
        ];
    }

    /**
     * Prazo com hora (tarefa) vem em UTC e vai no fuso do usuário; prazo só de data
     * (oportunidade) vai como Y-m-d, sem conversão, igual ao date_due da entidade.
     */
    private function formatDueDate(?string $date, string $timezone): ?string
    {
        if (!$date) {
            return null;
        }

        $model = new ($this->changeable_type);

        return $model->dueDateHasTime()
            ? DateTimeConversionService::convertFromUtc($date, $timezone)
            : substr($date, 0, 10);
    }
}
