<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ServiceRequest extends FormRequest
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
            'category' => 'nullable|string|max:255',
            'name' => 'sometimes|string|max:255',
            'labor_hours' => 'sometimes|numeric|min:0',
            'labor_hourly_rate' => 'sometimes|numeric|min:0',
            'labor_hourly_total' => 'sometimes|numeric|min:0',
            'profit_percentage' => 'sometimes|numeric|min:0|max:100',
            'profit' => 'sometimes|numeric|min:0',
            'price' => 'sometimes|numeric|min:0',
            'observations' => 'nullable|string',
        ];
    }

    protected function prepareForValidation()
    {
        $user = Auth::user();
        $this->merge([
            'account_id' => $user->account_id,
        ]);

        if ($this->has('labor_hours') && $this->has('labor_hourly_rate')) {
            $labor_hours_in_hours = $this->input('labor_hours') / 3600;
            $this->merge([
                'labor_hourly_total' => $labor_hours_in_hours * $this->input('labor_hourly_rate'),
            ]);
        }

        // if($this->has('profit_percentage') && $this->has('labor_hourly_total')) {
        //     $this->merge([
        //         'price' => $this->input('labor_hourly_total') / (1 - ($this->input('profit_percentage') / 100)),
        //     ]);
        //     $this->merge([
        //         'profit' => $this->input('price') - $this->input('labor_hourly_total'),
        //     ]);
        // }
        
        // if($this->has('profit_percentage') && $this->has('labor_hourly_total')) {
        //     $this->merge([
        //         'profit' => $this->input('labor_hourly_total') * ($this->input('profit_percentage') / 100),
        //     ]);
        // }

        // if($this->has('labor_hourly_total') && $this->has('profit')) {
        //     $this->merge([
        //         'price' => $this->input('labor_hourly_total') + $this->input('profit'),
        //     ]);
        // }
    }
}
