<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class RecurringExpenseRequest extends FormRequest
{
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
     * @return array
     */
    public function rules()
    {
        $required = $this->isMethod('post') ? 'required' : 'sometimes';

        return [
            'user_id' => 'nullable|exists:users,id',
            'department_id' => 'nullable|exists:departments,id',
            'lead_id' => 'nullable|exists:leads,id',
            'company_id' => 'nullable|exists:companies,id',
            'name' => "{$required}|string|max:255",
            'description' => 'nullable|string|max:1000',
            'category' => 'nullable|string|in:fixed,variable',
            'amount' => "{$required}|numeric|min:0.01",
            'due_day' => "{$required}|integer|min:1|max:31",
            'is_active' => 'boolean',
            'start_date' => "{$required}|date",
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'user_id' => $this->user_id ?: Auth::id(),
        ]);
    }

    public function messages()
    {
        return [
            'name.required' => 'O nome da despesa é obrigatório.',
            'amount.required' => 'O valor é obrigatório.',
            'amount.min' => 'O valor deve ser maior que zero.',
            'due_day.required' => 'O dia de vencimento é obrigatório.',
            'due_day.min' => 'O dia de vencimento deve ser entre 1 e 31.',
            'due_day.max' => 'O dia de vencimento deve ser entre 1 e 31.',
            'start_date.required' => 'A data de início é obrigatória.',
            'end_date.after_or_equal' => 'A data de término deve ser igual ou posterior à data de início.',
            'category.in' => 'A categoria deve ser "fixed" ou "variable".',
        ];
    }

    public function attributes()
    {
        return [
            'user_id' => 'usuário',
            'department_id' => 'departamento',
            'lead_id' => 'pessoa',
            'company_id' => 'empresa',
            'name' => 'nome',
            'description' => 'descrição',
            'category' => 'categoria',
            'amount' => 'valor',
            'due_day' => 'dia de vencimento',
            'start_date' => 'data de início',
            'end_date' => 'data de término',
        ];
    }
}
