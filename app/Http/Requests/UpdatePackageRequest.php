<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePackageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $packageId = $this->route('package');
        
        return [
            'PackageName' => 'sometimes|required|string|max:150',
            'PackageCode' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('packages', 'PackageCode')->ignore($packageId, 'PackageID')
            ],
            'Description' => 'nullable|string',
            'Price' => 'sometimes|required|numeric|min:0',
            'Interval' => 'nullable|string|max:255',
            'DiscountAmount' => 'nullable|numeric|min:0',
            'AdditionAmount' => 'nullable|numeric|min:0',
            'Status' => 'sometimes|required|in:Active,Inactive',
            // 'services' => 'nullable|array',
            // 'services.*.PackageServiceID' => 'sometimes|uuid|exists:package_services,PackageServiceID',
            // 'services.*.TreatmentTypeID' => 'nullable|uuid|exists:TreatmentTypeHierarchy,TreatmentTypeID',
            // 'services.*.TreatmentName' => 'required|string',
            // 'services.*.QuantityLimit' => 'required_with:services|integer|min:1',
            
        ];
    }
}
