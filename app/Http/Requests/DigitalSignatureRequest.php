<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class DigitalSignatureRequest
 * 
 * @package App\Http\Requests
 */
class DigitalSignatureRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'PatientID' => 'required|string|uuid',
            'ProviderID' => 'nullable|string|uuid',
            'signatureData' => 'required|string'
        ];
    }

    /**
     * Get the custom error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'PatientID.required' => 'The patient ID is required.',
            'PatientID.uuid' => 'The patient ID must be a valid UUID.',
            'ProviderID.uuid' => 'The provider ID must be a valid UUID.',
            'patientName.required' => 'The patient name is required.',
            'patientName.max' => 'The patient name may not be greater than 255 characters.',
            'signatureData.required' => 'The signature data is required.'
        ];
    }
}
