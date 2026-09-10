<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreditCardChargeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'credit_card_id' => 'required|exists:credit_cards,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'purchase_date' => 'required|date',
            'category' => 'nullable|string|max:255',
            'installment_total' => 'nullable|integer|min:1|max:60',
        ];
    }

    public function messages()
    {
        return [
            'credit_card_id.required' => 'O cartão é obrigatório.',
            'credit_card_id.exists' => 'O cartão selecionado não existe.',
            'description.required' => 'A descrição é obrigatória.',
            'amount.required' => 'O valor é obrigatório.',
            'amount.numeric' => 'O valor deve ser um número.',
            'amount.min' => 'O valor deve ser maior que zero.',
            'purchase_date.required' => 'A data da compra é obrigatória.',
            'purchase_date.date' => 'A data da compra deve ser uma data válida.',
            'installment_total.integer' => 'O número de parcelas deve ser um número inteiro.',
            'installment_total.min' => 'O número de parcelas deve ser pelo menos 1.',
            'installment_total.max' => 'O número de parcelas não pode ser maior que 60.',
        ];
    }

    public function attributes()
    {
        return [
            'credit_card_id' => 'cartão',
            'description' => 'descrição',
            'amount' => 'valor',
            'purchase_date' => 'data da compra',
            'category' => 'categoria',
            'installment_total' => 'número de parcelas',
        ];
    }
}
