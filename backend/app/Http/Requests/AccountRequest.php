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
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:accounts',
            'owner_id' => 'required|integer|exists:users,id',
            'logo' => 'image|max:2048',
            'subscription_status' => 'required|string|max:255',
            'expiration_date' => 'required|date',
            'is_active' => 'required|boolean',
        ];
    }
}
