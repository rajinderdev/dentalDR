<?php

namespace App\Http\Resources;

use App\Models\PatientExaminationDiagnosis;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientExaminationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'ExaminationID' => $this->PatientExaminationID,
            'PatientID' => $this->PatientID,
            'DateOfDiagnosis' => $this->DateOfDiagnosis,
            'ProviderID' => $this->ProviderID,
            'ChiefComplaint' => $this->ChiefComplaint,
            'PatientDiagnosisNotes' => $this->PatientDiagnosisNotes,
            'IsDeleted' => $this->IsDeleted,
            'CreatedOn' => $this->CreatedOn,
            'CreatedBy' => $this->CreatedBy,
            'LastUpdatedOn' => $this->LastUpdatedOn,
            'LastUpdatedBy' => $this->LastUpdatedBy,
            'Diagnosis' => PatientExaminationDiagnosisResource::collection($this->whenLoaded('diagnosis')),
        ];
    }
}
