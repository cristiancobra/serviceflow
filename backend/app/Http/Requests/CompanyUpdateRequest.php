<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\NormalizesDigits;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyUpdateRequest extends FormRequest
{
    use NormalizesDigits;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'legal_name' => ['filled', Rule::unique('companies')->ignore($this->route('company'))],
            'business_name' => 'nullable',
            'cnpj' => 'nullable|numeric|digits:14',
            'email' => 'email|nullable',
            'phone' => 'nullable|string|max:20',
            'cel_phone' => 'nullable|numeric|digits_between:10,11',
            'pix_key' => 'nullable|string|max:77',
            'linkedin' => 'nullable',
            'facebook' => 'nullable',
            'address' => 'nullable|string|max:255',
            'complement' => 'nullable|string|max:255',
            'neighborhood' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:20',
            'ibge_city_code' => 'nullable|digits:7',
            'instagram' => 'nullable',
            'other_social_media' => 'nullable',
            'contact_date' => 'nullable|date',
            'source' => 'nullable',
            'source_contact_channel' => 'nullable',
            'reason_for_initial_contact' => 'nullable',
            'comments' => 'nullable',
        ];
    }

    protected function prepareForValidation()
    {
        $this->normalizeDigits(['ibge_city_code']);
    }

    public function messages()
    {
        return [
            'ibge_city_code.digits' => 'O código IBGE do município deve ter 7 dígitos.',
        ];
    }
}
