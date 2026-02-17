<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommunicationGroupMasterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
  
    public function rules()
    {
        return [
            'GroupName'                   => 'required|string|max:255',
            'ClinicID'                    => 'required|string|max:255',
            'GroupType'                   => 'required|integer',
            'GroupDescription'            => 'required|string',
            'IsPatientGroup'              => 'nullable|boolean',
            'IsOtherGroup'                => 'nullable|boolean',
        ];
    }
}