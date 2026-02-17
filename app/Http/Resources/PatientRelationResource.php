<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientRelationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->PatientRelationID,
            'patient_id' => $this->PatientID,
            'related_patient_id' => $this->RelatedPatientID,
            'relation' => $this->Relation,
            'notes' => $this->Notes,
            'is_active' => (bool) $this->IsActive,
            'created_by' => $this->CreatedBy,
            'created_on' => $this->CreatedOn?->format('Y-m-d H:i:s'),
            'last_updated_by' => $this->LastUpdatedBy,
            'last_updated_on' => $this->LastUpdatedOn?->format('Y-m-d H:i:s'),
            
            // Include related patient data if loaded
            'patient' => $this->whenLoaded('patient', function () {
                return [
                    'id' => $this->patient->PatientID,
                    'name' => $this->patient->FirstName . ' ' . $this->patient->LastName,
                    'email' => $this->patient->Email,
                    'phone' => $this->patient->Mobile
                ];
            }),
            
            'related_patient' => $this->whenLoaded('relatedPatient', function () {
                return [
                    'id' => $this->relatedPatient->PatientID,
                    'name' => $this->relatedPatient->FirstName . ' ' . $this->relatedPatient->LastName,
                    'email' => $this->relatedPatient->Email,
                    'phone' => $this->relatedPatient->Mobile,
                    'date_of_birth' => $this->relatedPatient->DateOfBirth?->format('Y-m-d')
                ];
            }),
        ];
    }
}
