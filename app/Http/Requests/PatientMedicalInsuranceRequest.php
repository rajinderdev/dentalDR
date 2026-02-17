<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PatientMedicalInsuranceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'PatientID' => [
                'required',
                'string',
                'exists:Patient,PatientID'
            ],
            'InsuranceProvider' => 'nullable|string|max:255',
            'PolicyNumber' => [
                'required',
                'string',
                'max:100'
            ],
            'ExpirationDate' => 'required|date',
            'Notes' => 'nullable|string',
            'IsActive' => 'sometimes|boolean',
        ];
    }

}
