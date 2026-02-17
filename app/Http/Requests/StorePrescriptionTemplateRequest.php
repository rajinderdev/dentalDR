<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePrescriptionTemplateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
			'ClinicId' => 'required|string|max:255',
			'TemplateName' => 'required|string',
			'MedicineId' => 'required|string|max:255',
			'MedicineName' => 'required|string',
			'FrequencyId' => 'required',
			'Dosage' => 'required|string',
			'Duration' => 'required|string',
			'DrugNote' => 'required|string',
			'IsDeleted' => 'required',
			'PrescriptionTemplateMasterID' => 'required|string|max:255',
			'Frequency' => 'required|string',
			'SequenceOrder' => 'required',
        ];
    }
}