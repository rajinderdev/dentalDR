<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientMedicalAttributeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
{
    return [
        'MedicalAttributes' => 'required|array',

        'MedicalAttributes.*.PatientID' => 'required|string|max:255',
        'MedicalAttributes.*.Date' => 'nullable|date',
        'MedicalAttributes.*.MedicalAttributesCategory' => 'required|string',
        'MedicalAttributes.*.MedicalAttributeValue' => 'required|string',
        'MedicalAttributes.*.LastUpdatedBy' => 'nullable|string',
        'MedicalAttributes.*.LastUpdatedOn' => 'nullable|date',
    ];
}

}