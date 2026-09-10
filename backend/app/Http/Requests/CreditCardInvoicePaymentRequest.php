<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreditCardInvoicePaymentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
            'method' => 'required|string|in:cash,bank_transfer,pix,credit_card,debit_card,check',
        ];
    }

    public function messages()
    {
        return [
            'bank_account_id.required' => 'A conta bancária é obrigatória.',
            'bank_account_id.exists' => 'A conta bancária selecionada não existe.',
            'amount.required' => 'O valor é obrigatório.',
            'amount.numeric' => 'O valor deve ser um número.',
            'amount.min' => 'O valor deve ser maior que zero.',
            'transaction_date.required' => 'A data do pagamento é obrigatória.',
            'transaction_date.date' => 'A data do pagamento deve ser uma data válida.',
            'method.required' => 'O método de pagamento é obrigatório.',
            'method.in' => 'O método de pagamento selecionado é inválido.',
        ];
    }

    public function attributes()
    {
        return [
            'bank_account_id' => 'conta bancária',
            'amount' => 'valor',
            'transaction_date' => 'data do pagamento',
            'method' => 'método de pagamento',
        ];
    }
}
