<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSMSSituationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
			'SitutationCode' => 'required|string',
			'SituationDescription' => 'required|string',
			'DetailedTrigerringDeescription' => 'required|string',
			'SituationType' => 'required|string',
			'DependentField1' => 'nullable|string',
			'DependentField2' => 'nullable|string',
			'DependentField3' => 'nullable|string',
			'DependentField4' => 'nullable|string',
			'IsActive' => 'required',
			'isDeleted' => 'required'
        ];
    }
}