<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Services\DateConversionService;
use HTMLPurifier;
use HTMLPurifier_Config;

class ProjectRequest extends FormRequest
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
            'account_id' => 'required|exists:accounts,id',
            'user_id' => 'sometimes|exists:users,id',
            'name' => 'sometimes|unique:projects|required',
            'type' => 'nullable|string|max:255',
            'budget' => 'nullable|numeric',
            'actual_cost' => 'nullable|numeric',
            'priority' => 'nullable|string',
            'status' => 'nullable|string|max:255',
            'objective' => 'nullable|string|max:255',
            'date_start' => 'nullable|date',
            'date_due' => 'nullable|date',
            'date_conclusion' => 'nullable|date',
            'contact_id' => 'nullable|exists:leads,id',
            'company_id' => 'nullable|exists:companies,id',
            'goal_id' => 'nullable|exists:goals,id',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ];

    }

    protected function prepareForValidation()
    {
        $user = Auth::user();
        $this->merge([
            'account_id' => $user->account_id,
        ]);

        if ($this->has('description')) {
            $config = HTMLPurifier_Config::createDefault();
            $purifier = new HTMLPurifier($config);
            $sanitizedDescription = $purifier->purify($this->input('description'));

            $this->merge([
                'description' => $sanitizedDescription,
            ]);
        }

        if ($this->has('date_start')) {
            $this->merge([
                'date_start' => DateConversionService::convertToUtc($this->input('date_start')),
            ]);
        }

        if ($this->has('date_due')) {
            $this->merge([
                'date_due' => DateConversionService::convertToUtc($this->input('date_due')),
            ]);
        }

        if ($this->has('date_conclusion')) {
            $this->merge([
                'date_conclusion' => DateConversionService::convertToUtc($this->input('date_conclusion')),
            ]);
        }
    }
}