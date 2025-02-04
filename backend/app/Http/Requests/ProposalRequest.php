<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

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
            'status' => 'nullable|string',
            'installment_quantity' => 'required|integer|min:1|max:99',
            // 'services' => 'nullable|array',
            // 'services.*.id' => 'required|exists:services,id',
            // 'services.*.quantity' => 'required|integer|min:0',
            // 'services.*.category' => 'nullable|string',
            // 'services.*.name' => 'nullable|string',
            // 'services.*.labor_hours' => 'nullable|numeric',
            // 'services.*.labor_hourly_rate' => 'nullable|numeric',
            // 'services.*.labor_hourly_total' => 'nullable|numeric',
            // 'services.*.profit_percentage' => 'nullable|numeric',
            // 'services.*.profit' => 'nullable|numeric',
            // 'services.*.price' => 'nullable|numeric',
            // 'services.*.observations' => 'nullable|string',
            // 'services.*.account_id' => 'nullable|exists:accounts,id',
        ];
    }

    protected function prepareForValidation()
    {
        $user = Auth::user();
        $this->merge([
            'account_id' => $user->account_id,
        ]);

        $this->merge([
            'user_id' => $user->id,
        ]);
    }
}
