<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\DateConversionService;

class TaskStoreRequest extends FormRequest
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
            'name' => 'unique:tasks|required',
            'user_id' => 'required|exists:users,id', 
            'description' => 'nullable|string',
            'priority' => 'nullable|string',
            'status' => 'nullable|string',
            'date_start' => 'nullable|date',
            'date_due' => 'nullable|date|after_or_equal:start',
            'date_conclusion' => 'nullable|date|after_or_equal:start',
            'duration' => 'nullable|integer',
        ];
    }

    protected function prepareForValidation()
    {
        // Ajuste a data para o formato do banco de dados e converta para UTC usando o serviço
        if ($this->has('start')) {
            $this->merge([
                'start' => DateConversionService::convertToUtc($this->input('start')),
            ]);
        }

        if ($this->has('end')) {
            $this->merge([
                'end' => DateConversionService::convertToUtc($this->input('end')),
            ]);
        }
    }
}