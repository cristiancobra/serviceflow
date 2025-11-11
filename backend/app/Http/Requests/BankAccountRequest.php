<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\BankAccount;

class BankAccountRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $bankAccountId = $this->route('bank_account');
        
        return [
            'account_id' => 'required|exists:accounts,id',
            'user_id' => 'nullable|exists:users,id',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'bank_name' => 'required|string|max:255',
            'agency' => 'nullable|string|max:50',
            'initial_balance' => 'nullable|numeric|min:0',
            'type' => 'required|in:' . implode(',', array_keys(BankAccount::getTypeOptions())),
            'is_active' => 'boolean',
            'description' => 'nullable|string|max:1000',
        ];
    }

    protected function prepareForValidation()
    {
        $user = Auth::user();
        $this->merge([
            'account_id' => $user->account_id,
        ]);
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'account_id.required' => 'A conta é obrigatória.',
            'account_id.exists' => 'A conta selecionada não existe.',
            'user_id.exists' => 'O usuário selecionado não existe.',
            'account_name.required' => 'O nome da conta bancária é obrigatório.',
            'account_name.max' => 'O nome da conta bancária não pode ter mais de 255 caracteres.',
            'account_number.required' => 'O número da conta é obrigatório.',
            'account_number.max' => 'O número da conta não pode ter mais de 50 caracteres.',
            'bank_name.required' => 'O nome do banco é obrigatório.',
            'bank_name.max' => 'O nome do banco não pode ter mais de 255 caracteres.',
            'agency.max' => 'A agência não pode ter mais de 50 caracteres.',
            'balance.numeric' => 'O saldo deve ser um número válido.',
            'balance.min' => 'O saldo não pode ser negativo.',
            'type.required' => 'O tipo de conta é obrigatório.',
            'type.in' => 'O tipo de conta selecionado é inválido.',
            'is_active.boolean' => 'O status ativo deve ser verdadeiro ou falso.',
            'description.max' => 'A descrição não pode ter mais de 1000 caracteres.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'account_id' => 'conta',
            'user_id' => 'usuário',
            'account_name' => 'nome da conta',
            'account_number' => 'número da conta',
            'bank_name' => 'banco',
            'agency' => 'agência',
            'balance' => 'saldo',
            'type' => 'tipo',
            'is_active' => 'ativo',
            'description' => 'descrição',
        ];
    }
}
