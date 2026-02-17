<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientPrescriptionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            // Basic prescription fields
            'PatientID' => 'required|string|max:255',
            'ProviderID' => 'required|string|max:255',
            'DateOfPrescription' => 'required|date',
            'doctor' => 'nullable|string|max:255',
            
            // Prescription details
            'PrescriptionNote' => 'nullable|string',
            'NextFollowUp' => 'nullable|string|max:255',
            'notifyFollowUp' => 'nullable|boolean',
            'InvestigationAdvisedIDCSV' => 'nullable|string|max:255',
            'PatientInvestigationID' => 'nullable|string|max:255',
            'IsDeleted' => 'nullable|boolean',
            
            // Template medicines from rxTemplate
            'rxTemplate' => 'nullable|array',
            'rxTemplate.template_name' => 'nullable|string',
            'rxTemplate.templates' => 'nullable|array',
            'rxTemplate.templates.*.medicine_id' => 'nullable|string',
            'rxTemplate.templates.*.medicine_name' => 'nullable|string',
            'rxTemplate.templates.*.frequency_id' => 'nullable|integer',
            'rxTemplate.templates.*.dosage' => 'nullable|string',
            'rxTemplate.templates.*.duration' => 'nullable|string',
            'rxTemplate.templates.*.drug_note' => 'nullable|string',
            'rxTemplate.templates.*.is_deleted' => 'nullable|boolean',
            
            // Custom medicines
            'medicines' => 'nullable|array',
            'medicines.*.medicine_name' => 'nullable|string',
            'medicines.*.dosage' => 'nullable|string',
            'medicines.*.frequency' => 'nullable|string',
            'medicines.*.duration' => 'nullable|string',
            'medicines.*.drug_note' => 'nullable|string',
            
            // Investigation data
            'weight' => 'nullable|string',
            'bp' => 'nullable|string',
            'fb' => 'nullable|string',
            'plsb' => 'nullable|string',
            'hdac' => 'nullable|string',
            'ldl' => 'nullable|string',
            'acr' => 'nullable|string',
            'retina' => 'nullable|string',
            'urine' => 'nullable|string',
            
            // Additional flags
            'withLipid' => 'nullable|boolean',
            'withUrin' => 'nullable|boolean',
            'printWithLetterhead' => 'nullable|boolean',
            'sendSMS' => 'nullable|boolean',
        ];
    }
}