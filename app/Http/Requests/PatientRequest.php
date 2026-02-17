<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PatientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, |array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "PatientCode" => 'nullable|string|max:50',
            "CaseID" => 'nullable|int',
            "PatientID" => 'nullable|string|max:50',
            "RegistrationDate" => 'nullable|date',
            "referenceNo" => 'nullable|string|max:100',
            'ClinicID' => 'required|uuid',
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
            'AddressLine1' => 'nullable|max:255',
            'AddressLine2' => 'nullable|string|max:255',
            'Street' => 'nullable|string|max:255',
            'Area' => 'nullable|string|max:255',
            'Building' => 'nullable|string|max:255',
            'Building_id' => 'nullable|exists:buildings,id',
            'Building_code' => 'nullable',
            'VIP' => 'nullable|boolean',
            'City' => 'nullable|max:50',
            'State' => 'nullable|max:50',
            'ZipCode' => 'nullable',
            'Country' => 'required|integer',
            'PhoneNumber' => 'nullable|string|max:50',
            'MobileNumber' => 'required|string|max:50',
            'SecondaryMobile' => 'nullable|string|max:50',
            'EmailAddress1' => 'required|email|max:100',
            'EmailAddress2' => 'nullable|email|max:100',
            'CommunicationType' => 'nullable|array',
            'SmsType' => 'nullable|boolean',
            'BloodGroup' => 'nullable|string|max:10',
            'PatientNotes' => 'nullable|string|max:1000',
            'IsDead' => 'nullable|boolean',
            'Status' => 'nullable|integer',
            'Family' => 'nullable|uuid',
            'ReferredBy' => 'nullable|uuid',
            'AbhaID' => 'nullable|string|max:50',
            'Age' => 'nullable|int',
            'Designation' => 'nullable|string',
            'Notify' => 'nullable|string',
            'MarriageAnniversary' => 'nullable|date',
            'CommunicationGroupMasterGuid' => 'nullable|string',
            'GroupType' =>'nullable|string',
            'GroupName' => 'nullable|string',
            'GroupDescription' => 'nullable|string',
                
        ];
    }
}
