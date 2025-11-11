<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class InvoiceRequest extends FormRequest
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
        // Regras para criação (store)
        if ($this->isMethod('post')) {
            return [
                'proposal_id' => 'required|exists:proposals,id',
                'user_id' => 'required|exists:users,id',
                'date_due' => 'required|date',
                'prices' => 'required|array',
                'prices.*' => 'required|numeric|min:0',
                'observations' => 'sometimes|string|nullable',
            ];
        }

        // Regras para atualização (update/patch)
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return [
                'proposal_id' => 'sometimes|exists:proposals,id',
                'user_id' => 'sometimes|exists:users,id',
                'date_due' => 'sometimes|date',
                'price' => 'sometimes|numeric|min:0',
                'observations' => 'sometimes|string|nullable',
                'status' => 'sometimes|string|in:pending,paid,overdue,cancelled',
            ];
        }

        return [];
    }

    protected function prepareForValidation()
    {
        $user = Auth::user();
        
        // Apenas adiciona account_id e user_id para criação
        if ($this->isMethod('post')) {
            $this->merge([
                'account_id' => $user->account_id,
                'user_id' => $user->id,
            ]);
        }
    }
}