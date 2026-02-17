<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientReceiptRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // If a single object was sent, wrap it into an array so wildcard rules ('*.field') work
        $input = $this->all();
        if (is_array($input)) {
            $keys = array_keys($input);
            $isList = ($keys === range(0, count($keys) - 1));
            if (!$isList) {
                $this->replace([$input]);
            }
        }
    }

    public function rules()
    {
        // Accept the new payload shape and nested invoices, not legacy receipt fields
        return [
            '*.ClinicID' => 'nullable|string',
            '*.patientName' => 'nullable|string',
            '*.receivedAmount' => 'required|numeric',
            '*.paymentDate' => 'required|string',
            '*.receiptNumber' => 'nullable|string',
            '*.ModeofPayment' => 'required|string',
            '*.receiptNotes' => 'nullable|string',
            '*.paymentDetails' => 'nullable|string',
            '*.cardType' => 'nullable|string',
            '*.cardLastFour' => 'nullable|string',
            '*.cardName' => 'nullable|string',
            '*.cardValidFrom' => 'nullable|string',
            '*.cardValidTo' => 'nullable|string',
            '*.InsuranceName' => 'nullable|string',
            '*.PolicyNumber' => 'nullable|string',
            '*.PolicyNotes' => 'nullable|string',

            '*.Invoices' => 'required|array|min:1',
            '*.Invoices.*.id' => 'required|string',
            '*.Invoices.*.patient_id' => 'nullable|string',
            '*.Invoices.*.patient_treatment_done_id' => 'nullable|string',
            '*.Invoices.*.treatment_total_cost' => 'nullable|numeric',
            '*.Invoices.*.treatment_balance' => 'nullable|numeric',
            '*.Invoices.*.invoice_code_prefix' => 'nullable|string',
            '*.Invoices.*.invoice_number' => 'nullable|string',
        ];
    }
}