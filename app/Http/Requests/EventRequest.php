<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Allow all authenticated users
    }

    public function rules(): array
    {
        $rules = [
            'Description' => 'required|string',
            'StartDateTime' => 'required|date',
            'EndDateTime' => 'required|date|after:StartDateTime',
            'Status' => 'required|string|max:50',
        ];

        // For PUT/PATCH requests, make fields optional
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules = [
                'Description' => 'sometimes|nullable|string',
                'StartDateTime' => 'sometimes|required|date',
                'EndDateTime' => 'sometimes|required|date|after:StartDateTime',
                'Status' => 'sometimes|nullable|string|max:50',
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'StartDateTime.required' => 'Start date and time is required',
            'StartDateTime.date' => 'Start date and time must be a valid date',
            'EndDateTime.required' => 'End date and time is required',
            'EndDateTime.date' => 'End date and time must be a valid date',
            'EndDateTime.after' => 'End date and time must be after start date and time',
            'Status.max' => 'Status cannot exceed 50 characters',
        ];
    }
}
