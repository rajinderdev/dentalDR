<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->TransactionID,
            'wallet_id' => $this->WalletID,
            'amount' => (float) $this->Amount,
            'transaction_type' => $this->TransactionType,
            'reference_type' => $this->ReferenceType,
            'reference_id' => $this->ReferenceID,
            'description' => $this->Description,
            'balance_before' => (float) $this->BalanceBefore,
            'balance_after' => (float) $this->BalanceAfter,
            'status' => $this->Status,
            'created_by' => $this->CreatedBy,
            'created_on' => $this->CreatedOn?->format('Y-m-d H:i:s'),
            
            // Include wallet data if loaded
            'wallet' => $this->whenLoaded('wallet', function () {
                return [
                    'id' => $this->wallet->WalletID,
                    'patient_id' => $this->wallet->PatientID,
                    'balance' => (float) $this->wallet->Balance,
                    'currency' => $this->wallet->Currency,
                ];
            }),
            
            // Include patient data if wallet and patient are loaded
            'patient' => $this->when(
                $this->relationLoaded('wallet') && $this->wallet->relationLoaded('patient'),
                function () {
                    return [
                        'id' => $this->wallet->patient->PatientID,
                        'name' => $this->wallet->patient->FullName ?? null,
                        'email' => $this->wallet->patient->Email ?? null,
                        'phone' => $this->wallet->patient->MobileNumber ?? null,
                    ];
                }
            ),
        ];
    }
}
