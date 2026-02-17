<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientPackageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->PatientPackageID,
            'patient_id' => $this->PatientID,
            'package_id' => $this->PackageID,
            'start_date' => $this->StartDate->format('Y-m-d'),
            'end_date' => $this->EndDate->format('Y-m-d'),
            'total_cost' => (float) $this->TotalCost,
            'payment_date' => $this->PaymentDate?$this->PaymentDate->format('Y-m-d H:i:s'):null,
            'amount_paid' => (float) $this->AmountPaid,
            'payment_mode' => $this->PaymentMode,
            'transaction_reference' => $this->TransactionReference,
            'payment_status' => $this->PaymentStatus ?? 'pending',
            'status' => $this->Status ?? 'active',
            'created_by' => $this->CreatedBy,
            'created_on' => $this->CreatedOn ? $this->CreatedOn->format('Y-m-d H:i:s') : null,
            'last_updated_by' => $this->LastUpdatedBy,
            'last_updated_on' => $this->LastUpdatedOn ? $this->LastUpdatedOn->format('Y-m-d H:i:s') : null,
        ];
    }
}
