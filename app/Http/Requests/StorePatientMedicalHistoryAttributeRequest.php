<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientMedicalHistoryAttributeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
			'MedicalAttributesCategory' => 'required|string',
			'MedicalAttributesID' => 'nullable|string|max:255',
			'MedicalAttributeValue' => 'required|string',
			'MedicalAttributeText' => 'nullable|string',
			'MedicalHistoryDate' => 'nullable|date'
        ];
    }
}