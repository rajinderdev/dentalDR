<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientInvoiceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    

    public function rules()
    {
        return [
            'ClinicID'   => 'required|string|max:255',
            'PatientID'   => 'required|string|max:255',
            'TreatmentCost'  => 'required|numeric',
            'Status'      => 'nullable|string|max:100',

            // Newly added missing fields as nullable
            'InvoiceNo'          => 'nullable|string|max:255',
            'InvoiceNumber'      => 'nullable|string|max:255',
            'ManualInvoiceNo'    => 'nullable|string|max:255',
            'InvoiceCodePrefix'  => 'nullable|string|max:255',
            'InvoiceDate'        => 'nullable|date',
            'TreatmentAddition'  => 'nullable|string',
            'TreatmentDiscount'  => 'nullable|numeric',
            'TreatmentTax'       => 'nullable|numeric',
            'TreatmentTotalCost' => 'nullable|numeric',
            'TreatmentTotalPayment' => 'nullable|numeric',
            'TreatmentBalance'   => 'nullable|numeric',
            'IsDeleted'          => 'nullable|boolean',
            'IsCancelled'        => 'nullable|boolean',
            'CancellationNotes'  => 'nullable|string',
            'CreatedOn'          => 'nullable|date',
            'CreatedBy'          => 'nullable|string|max:255',
            'rowguid'            => 'nullable|string|max:255',
            'LastUpdatedBy'      => 'nullable|string|max:255',
            'LastUpdatedOn'      => 'nullable|date',
            'Notes'              => 'nullable|string'
        ];
    }
}
