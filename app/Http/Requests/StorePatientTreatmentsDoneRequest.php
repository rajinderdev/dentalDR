<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientTreatmentsDoneRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
		return [
			'id' => 'nullable|string',
			'WaitingAreaID' => 'nullable|string',
			'ProviderID' => 'required|string|max:255',
			'TreatmentCost' => 'required_if:WaitingAreaID,null|nullable|numeric',
			'TreatmentBalance' => 'nullable|numeric',
			'TreatmentDiscount' => 'nullable|numeric',
			'TreatmentTotalCost' => 'required_if:WaitingAreaID,null|nullable|numeric',
			'TreatmentPayment' => 'required_if:WaitingAreaID,null|nullable|string',
			'TreatmentDate' => 'required_if:WaitingAreaID,null|nullable|date',
			'TreatmentAddition' => 'nullable|string',
			'TeethTreatment' => 'required_if:WaitingAreaID,null|nullable|array',
			'TeethTreatment.*' => 'string',
			'ParentPatientTreatmentDoneID' => 'nullable|string',
			// TreatmentSteps array validation
			'TreatmentSteps' => 'nullable|array',
			'TreatmentSteps.*.TreatmentTypeID' => 'required_with:TreatmentSteps|string|max:255',
			'TreatmentSteps.*.TreatmentSubTypeID' => 'nullable|string|max:255',
			'TreatmentSteps.*.TeethTreatmentNote' => 'nullable|string',
			'TreatmentSteps.*.TreatmentAddition' => 'nullable|string',
			// Root-level treatment fields (used when TreatmentSteps is empty)
			'TreatmentTypeID' => 'nullable|string|max:255',
			'TreatmentSubTypeID' => 'nullable|string|max:255',
			'TeethTreatmentNote' => 'nullable|string',
			'PatientPackageID' => 'nullable|string',
		];
    }
}