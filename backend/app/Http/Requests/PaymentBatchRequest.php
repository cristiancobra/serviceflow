<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\DateTimeConversionService;

class PaymentBatchRequest extends FormRequest
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
        return [
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'transaction_date' => 'required|date',
            // Cartão de crédito não entra: nele não há movimentação bancária única
            // a conciliar, cada pagamento vira uma compra própria na fatura do cartão.
            'method' => 'required|string|in:cash,bank_transfer,pix,debit_card,check',
            'description' => 'nullable|string|max:255',
            'items' => 'required|array|min:2',
            'items.*.invoice_id' => 'required|distinct|exists:invoices,id',
            'items.*.amount' => 'required|numeric|gt:0',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->filled('transaction_date')) {
            $timezone = auth()->user()->timezone ?? 'America/Sao_Paulo';

            $this->merge([
                'transaction_date' => DateTimeConversionService::convertToUtc(
                    $this->input('transaction_date'),
                    $timezone
                ),
            ]);
        }
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'bank_account_id.required' => 'Selecione a conta bancária.',
            'bank_account_id.exists' => 'A conta bancária selecionada não existe.',
            'transaction_date.required' => 'A data do pagamento é obrigatória.',
            'transaction_date.date' => 'A data do pagamento deve ser uma data válida.',
            'method.required' => 'O método de pagamento é obrigatório.',
            'method.in' => 'O método de pagamento selecionado é inválido.',
            'items.required' => 'Selecione as faturas a pagar.',
            'items.min' => 'Selecione pelo menos duas faturas para pagar em lote.',
            'items.*.invoice_id.distinct' => 'A mesma fatura foi selecionada mais de uma vez.',
            'items.*.invoice_id.exists' => 'Uma das faturas selecionadas não existe.',
            'items.*.amount.required' => 'Informe o valor de cada fatura.',
            'items.*.amount.gt' => 'O valor de cada fatura deve ser maior que zero.',
        ];
    }
}
