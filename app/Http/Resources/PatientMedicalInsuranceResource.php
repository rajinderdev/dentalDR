<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientMedicalInsuranceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->PatientMedicalInsuranceID,
            'patient_id' => $this->PatientID,
            'insurance_provider' => $this->InsuranceProvider,
            'policy_number' => $this->PolicyNumber,
            'expiration_date' => $this->ExpirationDate?->format('Y-m-d'),
            'notes' => $this->Notes,
            'is_active' => (bool) $this->IsActive,
            'is_deleted' => (bool) $this->IsDeleted,
            'created_by' => $this->CreatedBy,
            'created_on' => $this->CreatedOn?->format('Y-m-d H:i:s'),
            'last_updated_by' => $this->LastUpdatedBy,
            'last_updated_on' => $this->LastUpdatedOn?->format('Y-m-d H:i:s'),
        ];
    }
}
