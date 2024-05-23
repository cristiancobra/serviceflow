<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'legal_name' => 'unique:companies',
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
