<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\DateConversionService;
use HTMLPurifier;
use HTMLPurifier_Config;
use Illuminate\Support\Facades\Auth;

class TaskRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            'project_id' => 'nullable',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'nullable|string',
            'status' => 'nullable|string',
            'date_start' => 'nullable|date',
            'date_due' => 'nullable|date|after_or_equal:start',
            'date_conclusion' => 'nullable|date|after_or_equal:start',
            'duration' => 'nullable|integer',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            unset($rules['name']);
            unset($rules['user_id']);
        } else {
            $rules['name'] = 'unique:tasks|required';
            $rules['user_id'] = 'required|exists:users,id';
        }
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