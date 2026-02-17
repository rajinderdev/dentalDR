<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource{
    public function toArray($request){
    return [
        'id' => $this->PatientID,
        'title' => $this->Title,
        'first_name' => $this->FirstName,
        'last_name' => $this->LastName,
        'gender' => $this->Gender,
        'dob' => $this->DOB,
        'phone' => $this->PhoneNumber,
        'mobile' => $this->MobileNumber,
        'email' => $this->EmailAddress1,
        'email2' => $this->EmailAddress2,
        'occupation' => $this->Occupation,
        'case_id' => $this->CaseID,
        'patient_code' => $this->PatientCode,
        'age' => $this->Age,
        'registration_date' => $this->RegistrationDate,
        'referred_by' => $this->ReferredBy,
        'abha_id' => $this->AbhaID,
        'ProfilePhoto' => $this->ImagePath?asset($this->ImagePath):NULL,
        'Signatures' => $this->Signatures,
        'SignatureDate' => $this->SignatureDate,
        'AddedOn' => $this->AddedOn,   
        'VIP'=>$this->VIP,
		'Designation'=>$this->Designation,
		'Building'=>$this->Building,
		'Building_id'=>$this->Building_id,
		'Notify'=>$this->Notify,
		'MarriageAnniversary'=>$this->MarriageAnniversary,
        'secondary_email'=>$this->EmailAddress2,
        'secondary_phone'=>$this->MobileNumber,
        'family_group'=>$this->FamilyGroup,
        'blood_group'=>$this->BloodGroup,
        'marital_status'=>$this->MaritalStatus,
        'nationality'=>$this->Nationality,
        'religion'=>$this->Religion,
        'cast'=>$this->Cast,
        'area'=>$this->Area,
        'pincode'=>$this->Pincode,
        'address'=>$this->Address,
        'marriage_anniversary'=>$this->MarriageAnniversary,
        'IsActiveEmailSubscriber'=>$this->IsActiveEmailSubscriber,
        'IsActiveSMSSubscriber'=>$this->IsActiveSMSSubscriber,
        'IsDeleted'=>$this->IsDeleted,
    ];
}
}