<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class LeadCreateRequest extends FormRequest
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
            'email' => 'nullable|email|unique:leads,email|max:255',
            'cel_phone' => 'nullable|regex:/^\(?[0-9]{2}\)?[\s9]?[0-9]{4}-?[0-9]{4}$/',
            'linkedin' => 'nullable|url',
            'facebook' => 'nullable|url',
            'instagram' => 'nullable|url',
            'other_social_media' => 'nullable|string',
            'contact_date' => 'nullable|date',
            'source' => 'nullable|string|max:255',
            'source_contact_channel' => 'nullable|string|max:255',
            'reason_for_initial_contact' => 'nullable|string',
            'comments' => 'nullable|string',
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

    /**
     * Return messages
     */
    public function messages()
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.string' => 'O nome deve ser um texto válido.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'email.email' => 'O email deve ser um endereço válido (exemplo: contato@empresa.com).',
            'email.unique' => 'Este email já está cadastrado no sistema.',
            'email.max' => 'O email não pode ter mais de 255 caracteres.',
            'cel_phone.regex' => 'O telefone deve estar no formato válido: (11) 99999-9999 ou 11 99999-9999.',
            'linkedin.url' => 'O LinkedIn deve ser uma URL válida.',
            'facebook.url' => 'O Facebook deve ser uma URL válida.',
            'instagram.url' => 'O Instagram deve ser uma URL válida.',
            'contact_date.date' => 'A data de contato deve ser uma data válida.',
        ];
    }
}
