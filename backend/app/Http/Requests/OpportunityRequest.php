<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class OpportunityRequest extends FormRequest
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
            'account_id' => 'required|exists:accounts,id',
            'lead_id' => 'nullable|exists:leads,id',
            'user_id' => 'sometimes|exists:users,id',
            'company_id' => 'nullable|exists:companies,id',
            'name' => 'sometimes|string|max:255',
            'category' => 'nullable|string|max:255',
            'date_start' => 'nullable|date',
            'date_due' => 'nullable|date|after_or_equal:date_start',
            'date_conclusion' => 'nullable|date',
            'description' => 'nullable|string',
            'source' => 'nullable|string|max:255',
        ];
    }

    protected function prepareForValidation()
    {
        $user = Auth::user();
        $this->merge([
            'account_id' => $user->account_id,
        ]);
    }
}
