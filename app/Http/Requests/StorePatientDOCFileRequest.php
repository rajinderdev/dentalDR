<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientDOCFileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
			'PatientID' => 'required|string|max:255',
			'DocumentID' => 'required|string|max:255',
			'VersionNumber' => 'nullable|string',
			'RelatedVersionID' => 'nullable|string|max:255',
			'RelatedVersionNumber' => 'nullable|string',
			'DocumentType' => 'required|string|max:255',
			'StatusID' => 'required|string|max:255',
			'Description' => 'required|string',
			'file' => 'required',
			'PublishOn' => 'required|string',
			'ExpirationOn' => 'required|string',
			'ReferenceNo' => 'required|string',
        ];
    }
}