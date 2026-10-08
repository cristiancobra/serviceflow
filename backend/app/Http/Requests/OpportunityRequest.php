<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Concerns\ValidatesDueDateChange;
use Illuminate\Support\Facades\Auth;

class OpportunityRequest extends FormRequest
{
    use ValidatesDueDateChange;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return array_merge([
            'account_id' => 'required|exists:accounts,id',
            'lead_id' => 'nullable|exists:leads,id',
            'user_id' => 'sometimes|exists:users,id',
            'company_id' => 'nullable|exists:companies,id',
            'name' => 'sometimes|string|max:255',
            'category' => 'nullable|string|max:255',
            'date_start' => 'nullable|date',
            'date_due' => 'nullable|date|after_or_equal:date_start',
            'date_conclusion' => 'nullable|date',
            'date_canceled' => 'nullable|date',
            'description' => 'nullable|string',
            'duration_time' => 'nullable|integer',
            'source' => 'nullable|string|max:255',
        ], $this->dueDateChangeRules());
    }

    public function withValidator($validator)
    {
        $this->validateDueDatePostponement($validator, 'opportunity');
    }

    protected function prepareForValidation()
    {
        $user = Auth::user();
        $this->merge([
            'account_id' => $user->account_id,
        ]);

        // Colunas date (sem hora): guarda o dia escolhido no fuso do usuário
        $timezone = $user->timezone ?? 'America/Sao_Paulo';

        foreach (['date_start', 'date_due', 'date_conclusion', 'date_canceled'] as $field) {
            if ($this->filled($field)) {
                $this->merge([
                    $field => \App\Services\DateTimeConversionService::toLocalDate($this->input($field), $timezone),
                ]);
            }
        }
    }
}
