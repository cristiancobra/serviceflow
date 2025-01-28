<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class LinkRequest extends FormRequest
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
            'account_id' => 'required|exists:accounts,id',
            'user_id' => 'nullable|exists:users,id',
            'task_id' => 'nullable|exists:tasks,id',
            'opportunity_id' => 'nullable|exists:opportunities,id',
            'url' => 'required|url',
            'title' => 'required|string|max:255',
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