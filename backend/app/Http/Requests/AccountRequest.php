<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:accounts',
            'email' => 'sometimes|string|email|max:255',
            'phone' => 'sometimes|string|max:255',
            'address' => 'sometimes|string|max:255',
            'address_city' => 'sometimes|string|max:255',
            'cnpj' => 'nullable|numeric|digits:14',
            'inscricao_municipal' => 'sometimes|string|max:255',
            'owner_id' => 'sometimes|integer|exists:users,id',
            'logo' => 'image|max:2048',
            'subscription_status' => 'sometimes|string|max:255',
            'expiration_date' => 'sometimes|date',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
