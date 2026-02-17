<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PatientPackageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'PatientID' => 'required|uuid|exists:Patient,PatientID',
            'PackageID' => 'required|uuid|exists:packages,PackageID',
            'StartDate' => 'required|date|after_or_equal:today',
            'EndDate' => 'required|date|after:StartDate',
            'TotalCost' => 'required|numeric|min:0',
            'PaymentDate' => 'nullable|date',
            'AmountPaid' => 'nullable|numeric|min:0|lte:TotalCost',
            'PaymentMode' => ['nullable', Rule::in(['cash', 'card', 'upi', 'netbanking', 'other'])],
            'TransactionReference' => 'nullable|string|max:100',
            'PaymentStatus' => ['required', Rule::in(['pending', 'paid'])],
            'Status' => ['sometimes', 'required', Rule::in(['active', 'expired', 'cancelled'])],
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['StartDate'] = 'sometimes|date';
            $rules['EndDate'] = 'sometimes|date|after:StartDate';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'PatientID.required' => 'Patient is required',
            'PackageID.required' => 'Package is required',
            'StartDate.after_or_equal' => 'Start date must be today or a future date',
            'EndDate.after' => 'End date must be after start date',
            'AmountPaid.lte' => 'Amount paid cannot be greater than total cost',
        ];
    }
}
