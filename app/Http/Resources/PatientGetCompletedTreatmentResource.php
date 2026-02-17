<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PatientGetCompletedTreatmentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->PatientTreatmentDoneID,
            'patient_id' => $this->PatientID,
            'provider' => [
                'id' => $this->doctor->ProviderID ?? null,
                'name' => $this->doctor->ProviderName ?? 'Unknown',
            ],
            'treatment_cost' => $this->TreatmentCost,
            'treatment_total_cost' => $this->TreatmentTotalCost,
            'treatment_payment' => $this->TreatmentPayment,
            'treatment_balance' => $this->TreatmentBalance,
            'treatment_date' => $this->TreatmentDate,
            'is_archived' => $this->IsArchived,
            'teeth_treatment_note' => $this->TeethTreatmentNote,
        ];
    }
}
