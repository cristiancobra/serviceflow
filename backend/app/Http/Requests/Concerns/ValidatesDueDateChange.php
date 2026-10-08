<?php

namespace App\Http\Requests\Concerns;

use App\Models\DueDateChange;
use Illuminate\Validation\Rule;

/**
 * Adiar o prazo (date_due) de um registro existente exige um motivo,
 * para que o histórico em due_date_changes diga por que o prazo mudou.
 */
trait ValidatesDueDateChange
{
    protected function dueDateChangeRules(): array
    {
        return [
            'date_due_change_reason' => ['nullable', Rule::in(array_keys(DueDateChange::REASONS))],
            'date_due_change_note' => 'nullable|string|max:500',
        ];
    }

    /**
     * @param  \Illuminate\Validation\Validator  $validator
     * @param  string  $routeKey  parâmetro da rota com o model (ex: 'task')
     */
    protected function validateDueDatePostponement($validator, string $routeKey): void
    {
        $validator->after(function ($validator) use ($routeKey) {
            $model = $this->route($routeKey);

            if (!$model || !$this->filled('date_due') || !$model->isDueDatePostponement($this->input('date_due'))) {
                return;
            }

            if (!$this->filled('date_due_change_reason')) {
                $validator->errors()->add('date_due', 'Informe o motivo do adiamento do prazo.');
            } elseif ($this->input('date_due_change_reason') === 'other' && !$this->filled('date_due_change_note')) {
                $validator->errors()->add('date_due', 'Descreva o motivo do adiamento do prazo.');
            }
        });
    }
}
