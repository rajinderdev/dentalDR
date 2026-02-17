<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTreatmentLabWorkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'PatientTreatmentsDoneID' => 'required|uuid',
            'PatientID' => 'required|uuid',
            'ProviderID' => 'required|uuid',
            'PatientLabID' => 'nullable|uuid',
            'LabWorkDate' => 'nullable|date',
        ];
    }
}


