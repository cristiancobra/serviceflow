<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\NormalizesDigits;
use App\Models\Account;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountRequest extends FormRequest
{
    use NormalizesDigits;

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
            'zip_code' => 'nullable|digits:8',
            'ibge_city_code' => 'nullable|digits:7',
            'cnpj' => 'nullable|numeric|digits:14',
            'is_mei' => 'sometimes|boolean',
            'mei_annual_limit' => 'sometimes|numeric|min:0',
            'inscricao_municipal' => 'sometimes|string|max:255',
            'nfse_environment' => ['sometimes', Rule::in(Account::NFSE_ENVIRONMENTS)],
            'nfse_default_service_code' => 'nullable|digits:6',
            'nfse_dps_series' => 'sometimes|integer|min:1|max:99999',
            'nfse_next_dps_number' => 'sometimes|integer|min:1',
            'pix_key' => 'nullable|string|max:77',
            'theme_preference' => 'sometimes|in:light,dark,auto',
            'owner_id' => 'sometimes|integer|exists:users,id',
            'logo' => 'image|max:2048',
            'subscription_status' => 'sometimes|string|max:255',
            'expiration_date' => 'sometimes|date',
            'is_active' => 'sometimes|boolean',
        ];
    }

    protected function prepareForValidation()
    {
        $this->normalizeDigits(['zip_code', 'ibge_city_code', 'nfse_default_service_code']);
    }

    public function messages()
    {
        return [
            'zip_code.digits' => 'O CEP deve ter 8 dígitos.',
            'ibge_city_code.digits' => 'O código IBGE do município deve ter 7 dígitos.',
            'nfse_default_service_code.digits' => 'O código de tributação nacional deve ter 6 dígitos.',
        ];
    }
}
