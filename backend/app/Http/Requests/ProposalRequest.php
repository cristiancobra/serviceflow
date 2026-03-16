<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Services\DateTimeConversionService;

class ProposalRequest extends FormRequest
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
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
            'account_id' => 'required|exists:accounts,id',
            'date' => 'sometimes|date',
            'opportunity_id' => 'nullable|exists:opportunities,id',
            'total_profit_percentage' => 'nullable|numeric',
            'total_profit' => 'nullable|numeric',
            'total_discount' => 'nullable|numeric|min:0',
            'status' => 'nullable|string',
            'installment_quantity' => 'sometimes|integer|min:1|max:99',
            'paid_at' => 'nullable|date',
        ];
    }

    protected function prepareForValidation()
    {
        $user = Auth::user();
        $this->merge([
            'account_id' => $user->account_id,
        ]);
        $this->merge([
            'user_id' => auth()->id(),
        ]);

        if ($this->filled('date')) {
            $timezone = auth()->user()->timezone ?? 'America/Sao_Paulo';
    
            $this->merge([
                'date' => DateTimeConversionService::convertToUtc(
                    $this->input('date'),
                    $timezone
                ),
            ]);
        }

        if ($this->filled('paid_at')) {
            $timezone = auth()->user()->timezone ?? 'America/Sao_Paulo';
    
            $this->merge([
                'paid_at' => DateTimeConversionService::convertToUtc(
                    $this->input('paid_at'),
                    $timezone
                ),
            ]);
        }
    }
}
