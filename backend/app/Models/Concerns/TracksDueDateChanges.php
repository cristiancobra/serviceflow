<?php

namespace App\Models\Concerns;

use App\Models\DueDateChange;
use Carbon\Carbon;

/**
 * Registra em due_date_changes cada alteração do date_due de um prazo já
 * definido. O motivo vem de withDueDateChangeReason(), chamado pelo
 * controller antes do save().
 */
trait TracksDueDateChanges
{
    protected ?string $dueDateChangeReason = null;
    protected ?string $dueDateChangeNote = null;

    public static function bootTracksDueDateChanges()
    {
        static::updated(function ($model) {
            if (!$model->wasChanged('date_due')) {
                return;
            }

            $previous = $model->getOriginal('date_due');
            $new = $model->date_due;

            // Definir o primeiro prazo não é alteração; e numa coluna só de data
            // o mesmo dia com outra hora também não muda nada.
            if (!$previous || $model->sameDueDate($previous, $new)) {
                return;
            }

            $model->dueDateChanges()->create([
                'user_id' => auth()->id(),
                'previous_date' => $model->normalizeDueDate($previous),
                'new_date' => $new ? $model->normalizeDueDate($new) : null,
                'reason' => $model->dueDateChangeReason,
                'note' => $model->dueDateChangeNote,
            ]);
        });
    }

    public function dueDateChanges()
    {
        return $this->morphMany(DueDateChange::class, 'changeable')->orderBy('id');
    }

    public function withDueDateChangeReason(?string $reason, ?string $note = null): static
    {
        $this->dueDateChangeReason = $reason;
        $this->dueDateChangeNote = $note;

        return $this;
    }

    /**
     * Se mudar o prazo para $newDate é um adiamento (nova data depois da atual).
     */
    public function isDueDatePostponement($newDate): bool
    {
        if (!$this->date_due || !$newDate) {
            return false;
        }

        return Carbon::parse($this->normalizeDueDate($newDate))
            ->gt(Carbon::parse($this->normalizeDueDate($this->date_due)));
    }

    /**
     * Se a coluna date_due guarda hora (dateTime) ou só a data (date).
     */
    public function dueDateHasTime(): bool
    {
        return true;
    }

    protected function normalizeDueDate($date): string
    {
        $carbon = Carbon::parse($date);

        return $this->dueDateHasTime()
            ? $carbon->format('Y-m-d H:i:s')
            : $carbon->startOfDay()->format('Y-m-d H:i:s');
    }

    protected function sameDueDate($a, $b): bool
    {
        return $b && $this->normalizeDueDate($a) === $this->normalizeDueDate($b);
    }
}
