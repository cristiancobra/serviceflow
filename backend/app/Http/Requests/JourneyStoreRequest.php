<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\DateConversionService;
use HTMLPurifier;
use HTMLPurifier_Config;

class JourneyStoreRequest extends FormRequest
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
            'name' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
            'task_id' => 'required|exists:tasks,id',
            'details' => 'nullable|string',
            'start' => 'nullable|date',
            'end' => 'nullable|date|after_or_equal:start',
            'duration' => 'nullable|integer',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'user_id' => auth()->id(),
        ]);

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

        if ($this->has('start') && $this->has('end') && $this->input('end') !== null) {
            $this->merge([
                'duration' => $this->calculateDuration(
                    $this->input('start'),
                    $this->input('end')
                ),
            ]);
        }

        $journeyExists = $this->route('journey');

        if ($journeyExists && $journeyExists->duration !== null) {

            if ($this->has('start') && $this->input('start') !== $journeyExists->start) {
                $this->merge([
                    'duration' => $this->calculateDuration(
                        $this->input('start'),
                        $journeyExists->end
                    ),
                ]);
            }

            if ($this->has('end') && $this->input('end') !== $journeyExists->end) {
                if (!$this->has('start')) {
                    $this->merge([
                        'duration' => $this->calculateDuration(
                            $journeyExists->start,
                            $this->input('end')
                        ),
                    ]);
                }
            }
        }
    }

    private function calculateDuration($start, $end)
    {
        $startDate = new \DateTime($start);
        $endDate = new \DateTime($end);

        $interval = $startDate->diff($endDate);

        return $interval->h * 3600 + $interval->i * 60 + $interval->s;
    }
}
