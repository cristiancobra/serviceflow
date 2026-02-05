<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Services\DateTimeConversionService;

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
                'lead_id' => 'required|exists:leads,id',
                'date_due' => 'required|date',
                'price' => 'sometimes|numeric|min:0',
                'prices' => 'sometimes|array',
                'prices.*' => 'sometimes|numeric|min:0',
                'type' => 'sometimes|string|in:credit,debit',
                'observations' => 'sometimes|string|nullable',
            ];
        }

        // Regras para atualização (update/patch)
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return [
                'proposal_id' => 'sometimes|exists:proposals,id',
                'user_id' => 'sometimes|exists:users,id',
                'lead_id' => 'sometimes|exists:leads,id',
                'date_due' => 'sometimes|date',
                'price' => 'sometimes|numeric|min:0',
                'type' => 'sometimes|string|in:credit,debit',
                'observations' => 'sometimes|string|nullable',
                'status' => 'sometimes|string|in:pending,partial,paid,overdue,cancelled',
            ];
        }

        return [];
    }

    protected function prepareForValidation()
    {
        $user = Auth::user();
        
        // Adiciona account_id e user_id para criação
        if ($this->isMethod('post')) {
            $mergeData = [
                'account_id' => $user->account_id,
                'user_id' => $user->id,
            ];
            
            // Define tipo padrão como 'credit' se não for fornecido
            if (!$this->has('type')) {
                $mergeData['type'] = 'credit';
            }
            
            $this->merge($mergeData);
        }

        if ($this->filled('date_due')) {
            $this->merge([
                'date_due' => DateTimeConversionService::convertJavascriptDate($this->input('date_due')),
            ]);
        }
    }
}