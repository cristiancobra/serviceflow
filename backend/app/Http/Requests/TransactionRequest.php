<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\DateTimeConversionService;

class TransactionRequest extends FormRequest
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
            'invoice_id' => 'sometimes|required|exists:invoices,id',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'amount' => 'sometimes|required|numeric|min:0',
            'transaction_date' => 'sometimes|required|date',
            'type' => 'sometimes|required|string|in:credit,debit',
            'method' => 'sometimes|required|string|in:cash,bank_transfer,pix,credit_card,debit_card,check',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'user_id' => auth()->id(),
        ]);

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
            'invoice_id.required' => 'A fatura é obrigatória.',
            'invoice_id.exists' => 'A fatura selecionada não existe.',
            'amount.required' => 'O valor é obrigatório.',
            'amount.numeric' => 'O valor deve ser um número.',
            'amount.min' => 'O valor deve ser maior que zero.',
            'transaction_date.required' => 'A data da transação é obrigatória.',
            'transaction_date.date' => 'A data da transação deve ser uma data válida.',
            'type.required' => 'O tipo de transação é obrigatório.',
            'type.in' => 'O tipo de transação deve ser crédito ou débito.',
            'method.required' => 'O método de pagamento é obrigatório.',
            'method.in' => 'O método de pagamento selecionado é inválido.',
        ];
    }
}