<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class InvoiceRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'proposal_id' => 'required|exists:proposals,id',
            'user_id' => 'required|exists:users,id',
            'date_due' => 'required|date',
            'prices' => 'required|array',
            'prices.*' => 'required|numeric|min:0',
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