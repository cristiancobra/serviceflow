<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CostRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'account_id' => 'required|integer|exists:accounts,id',
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