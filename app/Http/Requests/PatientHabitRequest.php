<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PatientHabitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // If the request is a string (JSON array), decode it
        if (is_string($this->input())) {
            $this->merge(json_decode($this->input(), true));
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        // Check if the request is an array of items
        if ($this->isMethod('post') && is_array($this->all()) && array_key_exists(0, $this->all())) {
            return [
                '*.PatientID' => [
                    'required',
                    'string',
                    'exists:Patient,PatientID'
                ],
                '*.HabitID' => [
                    'required',
                    'string',
                    'exists:habits,HabitID'
                ],
                '*.Notes' => 'nullable|string|max:1000',
                '*.IsActive' => 'boolean'
            ];
        }

        // Single item validation
        return [
            'PatientID' => [
                'required',
                'string',
                'exists:Patient,PatientID'
            ],
            'HabitID' => [
                'required',
                'string',
                'exists:habits,HabitID'
            ],
            'Notes' => 'nullable|string|max:1000',
            'IsActive' => 'boolean'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            '*.PatientID.required' => 'The patient ID field is required.',
            '*.HabitID.required' => 'The habit ID field is required.',
            '*.HabitID.exists' => 'The selected habit ID is invalid.',
            'PatientID.required' => 'The patient ID field is required.',
            'HabitID.required' => 'The habit ID field is required.',
            'HabitID.exists' => 'The selected habit ID is invalid.',
            'HabitID.unique' => 'This habit is already assigned to the patient.',
            'EndDate.after_or_equal' => 'The end date must be after or equal to the start date.',
        ];
    }
}
