<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TreatmentLabWorkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'treatment_lab_work_id' => $this->TreatmentLabWorkID,
            'patient_treatments_done_id' => $this->PatientTreatmentsDoneID,
            'patient_id' => $this->PatientID,
            'provider_id' => $this->ProviderID,
            'patient_lab_id' => $this->PatientLabID,
            'lab_work_date' => optional($this->LabWorkDate)->format('Y-m-d'),
            'created_on' => optional($this->CreatedOn)->toDateTimeString(),
            'created_by' => $this->CreatedBy,
            'last_updated_on' => optional($this->LastUpdatedOn)->toDateTimeString(),
            'last_updated_by' => $this->LastUpdatedBy,
        ];
    }
}


