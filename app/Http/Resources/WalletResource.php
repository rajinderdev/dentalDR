<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->WalletID,
            'patient_id' => $this->PatientID,
            'family_id' => $this->FamilyID,
            'balance' => (float) $this->Balance,
            'currency' => $this->Currency,
            'is_active' => (bool) $this->IsActive,
            'last_transaction_date' => $this->LastTransactionDate?->format('Y-m-d H:i:s'),
            'created_by' => $this->CreatedBy,
            'created_on' => $this->CreatedOn?->format('Y-m-d H:i:s'),
            'last_updated_by' => $this->LastUpdatedBy,
            'last_updated_on' => $this->LastUpdatedOn?->format('Y-m-d H:i:s'),
            
            // Include patient data if loaded
            'patient' => $this->whenLoaded('patient', function () {
                return [
                    'id' => $this->patient->PatientID,
                    'name' => $this->patient->FullName ?? null,
                    'email' => $this->patient->Email ?? null,
                    'phone' => $this->patient->MobileNumber ?? null,
                ];
            }),
            
            // Include transactions if loaded
            'transactions' => TransactionResource::collection($this->whenLoaded('transactions')),
            
            // Include summary if needed
            'summary' => $this->when($this->relationLoaded('transactions'), function () {
                return [
                    'total_credits' => (float) $this->total_credits,
                    'total_debits' => (float) $this->total_debits,
                    'transaction_count' => $this->transactions->count(),
                ];
            }),
        ];
    }
}
