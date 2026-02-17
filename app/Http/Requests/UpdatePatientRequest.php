<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "PatientCode" => 'nullable|string|max:50',
            "CaseID" => 'nullable|int',
            "PatientID" => 'nullable|string|max:50',
            "RegistrationDate" => 'nullable|date',
            "PatientRefNo" => 'nullable|string|max:100',
            'ProviderID' => 'required|uuid',
            'Title' => 'required|string|max:50',
            'FirstName' => 'required|string|max:50',
            'LastName' => 'nullable|string|max:50',
            'MiddleName' => 'nullable|string|max:50',
            'Gender' => 'nullable|string|in:M,F,O',
            'DOB' => 'nullable|date',
            'patientGroup' => 'nullable|uuid',
            'NationalityOther' => 'nullable|string|max:100',
            'FamilyID' => 'nullable|uuid',
            'RefferdGroup' => 'nullable|string|max:100',
            'Occupation' => 'nullable|string|max:100',
            'Nationality' => 'nullable|string|max:100',
            'AddressLine1' => 'nullable|string|max:255',
            'AddressLine2' => 'nullable|string|max:255',
            'Street' => 'nullable|string|max:255',
            'Area' => 'nullable|string|max:255',
            'City' => 'nullable|string|max:50',
            'State' => 'nullable|string|max:50',
            'ZipCode' => 'nullable|max:50',
            'Country' => 'nullable|integer',
            'PhoneNumber' => 'nullable|string|max:50',
            'MobileNumber' => 'nullable|string|max:50',
            'SecondaryMobile' => 'nullable|string|max:50',
            'EmailAddress1' => 'nullable|email|max:100',
            'EmailAddress2' => 'nullable|email|max:100',
            'CommunicationType' => 'nullable|array',
            'SmsType' => 'nullable|boolean',
            'BloodGroup' => 'nullable|string|max:10',
            'PatientNotes' => 'nullable|string|max:1000',
            'IsDead' => 'nullable|boolean',
            'Status' => 'nullable|integer',
            'Family' => 'nullable|uuid',
            'ReferredBy' => 'nullable',
            'AbhaID' => 'nullable|string|max:50',
            'Age' => 'nullable|int',
            'Designation' => 'nullable|string',
            'Notify' => 'nullable|string',
            'MarriageAnniversary' => 'nullable|date',
            'Building' => 'nullable|string|max:255',
            'Building_id' => 'nullable|exists:buildings,id',
            'Building_code' => 'nullable',
            'VIP' => 'nullable|boolean',
            'patientGroupmasterid' => 'nullable|string',
            'grouptype' =>'nullable|string',
            'groupname' => 'nullable|string',
            'groupdesc' => 'nullable|string',
        ];
    }
}