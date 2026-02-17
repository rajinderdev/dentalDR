<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientPackageUsageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->PatientPackageUsageID,
            'patient_package_id' => $this->PatientPackageID,
            'patient_treatment_done_id' => $this->PatientTreatmentDoneID,
            'provider_id' => $this->ProviderID,
            'patient_id' => $this->PatientID,
            'treatment_date' => $this->TreatmentDate?->format('Y-m-d'),
            'notes' => $this->Notes,
            'created_by' => $this->CreatedBy,
            'created_on' => $this->CreatedOn?->format('Y-m-d H:i:s'),
            'last_updated_by' => $this->LastUpdatedBy,
            'last_updated_on' => $this->LastUpdatedOn?->format('Y-m-d H:i:s'),
            'is_deleted' => (bool) $this->IsDeleted,
            'provider' => new ProviderResource($this->provider),
            'patient' => new PatientResource($this->patient),
            'patient_package' => new PatientPackageResource($this->patient_package),
            'patient_treatment_done' => new PatientTreatmentsDoneResource($this->patient_treatment_done),
        ];
    }
}
