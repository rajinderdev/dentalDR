<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePackageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'PackageName' => 'required|string|max:150',
            'PackageCode' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('packages', 'PackageCode')
            ],
            'Description' => 'nullable|string',
            'ClinicID' => 'nullable|string',
            'Price' => 'required|numeric|min:0',
            'Interval' => 'nullable|string|max:255',
            'DiscountAmount' => 'nullable|numeric|min:0',
            'AdditionAmount' => 'nullable|numeric|min:0',
            'Status' => 'required|in:Active,Inactive',
            // 'services' => 'nullable|array',
            // 'services.*.TreatmentTypeID' => 'nullable|uuid|exists:TreatmentTypeHierarchy,TreatmentTypeID',
            // 'services.*.TreatmentName' => 'required|string',
            // 'services.*.QuantityLimit' => 'required|integer|min:1'
        ];
    }
}
