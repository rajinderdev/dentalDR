<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\PatientPackageUsageResource;

class PatientWithAppointmentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'status' => true,
            'message' => 'Patient retrieved successfully',
            'data' => [
                'PatientID' => $this->PatientID,
                'ClinicID' => $this->ClinicID,
                'ProviderID' => $this->ProviderID,
                'FullName' => "{$this->Title} {$this->FirstName} {$this->LastName}",
                'title' => $this->Title,
                'FirstName' => $this->FirstName,
                'LastName' => $this->LastName,
                'Gender' => $this->Gender,
                'DOB' => $this->DOB ? $this->DOB->format('Y-m-d') : null,
                'Age' => $this->Age,
                'CaseID' => $this->CaseID,
                'RegistrationDate' => $this->RegistrationDate ? $this->RegistrationDate->format('Y-m-d') : null,
                'ReferredBy' => $this->ReferredBy,
                'Occupation' => $this->Occupation,
                'FamilyID' => $this->FamilyID,
                'PatientRefNo' => $this->PatientRefNo,
                'ProfilePhoto' => $this->ImagePath?asset($this->ImagePath):NULL,
                'Signatures' => $this->Signatures ? asset($this->Signatures) : NULL,
                'SignatureDate' => $this->SignatureDate,
                'Contact' => [
                    'Address' => [
                        'Line1' => $this->AddressLine1,
                        'Line2' => $this->AddressLine2,
                        'Street' => $this->Street,
                        'Area' => $this->Area,
                        'City' => $this->City,
                        'State' => $this->State,
                        'Country' => $this->Country,
                        'ZipCode' => $this->ZipCode,
                        'Building'=>$this->Building,
                        'Building_id'=>$this->Building_id,
                    ],
                    'PhoneNumber' => $this->PhoneNumber,
                    'MobileNumber' => $this->MobileNumber,
                    'Email' => $this->EmailAddress1,
                    'SecondaryEmail' => $this->EmailAddress2,
                    'SecondaryPhone' => $this->SecondaryMobile,
                ],
                'PatientCode' => $this->PatientCode,
                'Nationality' => $this->Nationality,
                'BloodGroup' => $this->BloodGroup,
                'Notes' => $this->PatientNotes,
                'IsDeceased' => $this->IsDead,
                'MedicalHistory' => PatientMedicalAttributeResource::collection($this->patient_medical_attributes),
                'DentalHistory' => PatientDentalHistoryAttributeResource::collection($this->patient_dental_history_attributes),
                'ReminderHistory' => PersonalReminderResource::collection($this->personal_reminders),
                'Allergy' => PatientAllergyAttributeResource::collection($this->patient_allergy_attributes),
                'Investigations' => PatientInvestigationResource::collection($this->patient_investigations),
                'Consent' => new PatientConsentDetailResource($this->consents),
                'PatientNotes' =>PatientNoteResource::collection($this->patient_notes),
                'PatientHabit' =>PatientHabitResource::collection($this->patient_habits),
                'PatientPrescription' =>PatientPrescriptionResource::collection($this->patient_prescriptions),
                'PatientPackageUsages' => PatientPackageUsageResource::collection($this->whenLoaded('patient_package_usages')),
                'VIP'=>$this->VIP,
                'Designation'=>$this->Designation,
                'Building'=>$this->Building,
                'Building_id'=>$this->Building_id,
                'Notify'=>$this->Notify,
                'MarriageAnniversary'=>$this->MarriageAnniversary,
                'FamilyID'=>$this->FamilyID,
                'IsActiveEmailSubscriber'=>$this->IsActiveEmailSubscriber,
                'IsActiveSMSSubscriber'=>$this->IsActiveSMSSubscriber,
                'IsDeleted'=>$this->IsDeleted,
                'Groupid'=>$this->patient_communication_group?$this->patient_communication_group->CommunicationGroupMasterGuid:null,
                // 'appointments' => AppointmentResource::collection($this->appointments),
            ]
        ];
    }
}
