<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreditCardRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'default_bank_account_id' => 'nullable|exists:bank_accounts,id',
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:50',
            'last_digits' => 'nullable|digits:4',
            'credit_limit' => 'nullable|numeric|min:0',
            'closing_day' => 'required|integer|min:1|max:31',
            'due_day' => 'required|integer|min:1|max:31',
            'is_active' => 'boolean',
            'description' => 'nullable|string|max:1000',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'user_id' => $this->user_id ?: Auth::id(),
            'default_bank_account_id' => $this->default_bank_account_id ?: null,
        ]);
    }

    public function messages()
    {
        return [
            'user_id.required' => 'O usuário responsável é obrigatório.',
            'user_id.exists' => 'O usuário selecionado não existe.',
            'default_bank_account_id.exists' => 'A conta bancária selecionada não existe.',
            'name.required' => 'O nome do cartão é obrigatório.',
            'name.max' => 'O nome do cartão não pode ter mais de 255 caracteres.',
            'last_digits.digits' => 'Os últimos dígitos devem ter exatamente 4 números.',
            'credit_limit.numeric' => 'O limite de crédito deve ser um número válido.',
            'credit_limit.min' => 'O limite de crédito não pode ser negativo.',
            'closing_day.required' => 'O dia de fechamento é obrigatório.',
            'closing_day.min' => 'O dia de fechamento deve ser entre 1 e 31.',
            'closing_day.max' => 'O dia de fechamento deve ser entre 1 e 31.',
            'due_day.required' => 'O dia de vencimento é obrigatório.',
            'due_day.min' => 'O dia de vencimento deve ser entre 1 e 31.',
            'due_day.max' => 'O dia de vencimento deve ser entre 1 e 31.',
            'description.max' => 'A descrição não pode ter mais de 1000 caracteres.',
        ];
    }

    public function attributes()
    {
        return [
            'user_id' => 'usuário',
            'default_bank_account_id' => 'conta bancária padrão',
            'name' => 'nome',
            'brand' => 'bandeira',
            'last_digits' => 'últimos dígitos',
            'credit_limit' => 'limite de crédito',
            'closing_day' => 'dia de fechamento',
            'due_day' => 'dia de vencimento',
            'description' => 'descrição',
        ];
    }
}
