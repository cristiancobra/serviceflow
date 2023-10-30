<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyCreateRequest extends FormRequest
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
            'legal_name' => 'unique:companies|required',
            'business_name' => 'nullable',
            'cnpj' => 'nullable|numeric|digits:14',
            'email' => 'email|nullable',
            'cel_phone' => 'nullable|numeric|digits:10',
            'linkedin' => 'nullable',
            'facebook' => 'nullable',
            'instagram' => 'nullable',
            'other_social_media' => 'nullable',
            'contact_date' => 'nullable|date',
            'source' => 'nullable',
            'source_contact_channel' => 'nullable',
            'reason_for_initial_contact' => 'nullable',
            'comments' => 'nullable',
        ];
    }
}
