<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PatientPackageUsageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'PatientPackageID' => 'required|uuid|exists:patient_package,PatientPackageID',
            'PatientTreatmentDoneID' => 'required|uuid|exists:patient_treatments_done,PatientTreatmentDoneID',
            'ProviderID' => 'required|uuid|exists:users,UserID',
            'TreatmentDate' => 'required|date|before_or_equal:today',
            'Notes' => 'nullable|string|max:1000'
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['PatientPackageID'] = 'sometimes|uuid|exists:patient_package,PatientPackageID';
            $rules['PatientTreatmentDoneID'] = 'sometimes|uuid|exists:patient_treatments_done,PatientTreatmentDoneID';
            $rules['ProviderID'] = 'sometimes|uuid|exists:users,UserID';
            $rules['TreatmentDate'] = 'sometimes|date|before_or_equal:today';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'PatientPackageID.required' => 'Patient package is required',
            'PatientPackageID.exists' => 'Selected patient package does not exist',
            'PatientTreatmentDoneID.required' => 'Treatment is required',
            'PatientTreatmentDoneID.exists' => 'Selected treatment does not exist',
            'ProviderID.required' => 'Provider is required',
            'ProviderID.exists' => 'Selected provider does not exist',
            'TreatmentDate.before_or_equal' => 'Treatment date cannot be in the future',
        ];
    }
}
