<?php

namespace App\Services;

use App\Models\PatientWallet;
use App\Models\WalletTransaction;
use App\Models\Patient;
use App\Models\PatientReceipt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WalletService
{
    /**
     * Get wallet by ID
     */
    public function getWalletById(string $walletId): ?PatientWallet
    {
        return PatientWallet::with(['patient', 'transactions'])
            ->where('IsDeleted', false)
            ->findOrFail($walletId);
    }

    /**
     * Get wallet by patient ID
     */
    public function getWalletByPatientId(string $patientId): ?PatientWallet
    {
        $patient = Patient::find($patientId);
        if ($patient) {
            return $this->getWalletForPatient($patient);
        }

        return PatientWallet::with(['patient', 'transactions'])
            ->where('PatientID', $patientId)
            ->where('IsDeleted', false)
            ->first();
    }

    /**
     * Family-first lookup: if patient belongs to a family and a family wallet exists,
     * return that wallet so family members share the same balance.
     */
    public function getWalletForPatient(Patient $patient): ?PatientWallet
    {
        $query = PatientWallet::with(['patient', 'transactions'])->where('IsDeleted', false);

        if (!empty($patient->FamilyID)) {
            $familyWallet = (clone $query)->where('FamilyID', $patient->FamilyID)->first();
            if ($familyWallet) {
                return $familyWallet;
            }
        }

        return $query->where('PatientID', $patient->PatientID)->first();
    }

    public function getOrCreateWalletForPatient(Patient $patient, ?string $createdBy = null): PatientWallet
    {
        $existing = $this->getWalletForPatient($patient);
        if ($existing) {
            return $existing;
        }

        $data = [
            'PatientID' => $patient->PatientID,
            'FamilyID' => $patient->FamilyID ?? null,
            'Currency' => 'INR',
            'IsActive' => true,
            'InitialBalance' => 0,
            'CreatedBy' => $createdBy,
        ];

        return $this->createWallet($data);
    }

    /**
     * Create a new wallet for a patient
     */
    public function createWallet(array $data): PatientWallet
    {
        return DB::transaction(function () use ($data) {
            $data['WalletID'] = (string) Str::uuid();
            $initialBalance = (float) ($data['InitialBalance'] ?? 0);
            $data['Balance'] = $initialBalance;
            unset($data['InitialBalance']);
            
            $wallet = PatientWallet::create($data);
            
            // Create initial transaction if initial balance is provided
            if ($initialBalance > 0) {
                $this->createTransaction([
                    'WalletID' => $wallet->WalletID,
                    'PatientID' => $wallet->PatientID,
                    'FamilyID' => $wallet->FamilyID,
                    'Amount' => $initialBalance,
                    'TransactionType' => 'CREDIT',
                    'ReferenceType' => 'INITIAL',
                    'ReferenceID' => null,
                    'Description' => 'Initial wallet balance',
                    'CreatedBy' => $data['CreatedBy'] ?? null,
                ]);
            }
            
            return $wallet->load('transactions');
        });
    }

    /**
     * Update wallet details
     */
    public function updateWallet(PatientWallet $wallet, array $data): PatientWallet
    {
        $wallet->update($data);
        return $wallet->fresh(['patient', 'transactions']);
    }

    /**
     * Delete a wallet (soft delete)
     */
    public function deleteWallet(PatientWallet $wallet): bool
    {
        return $wallet->update(['IsDeleted' => true]);
    }

    /**
     * Create a wallet transaction
     */
    public function createTransaction(array $data): WalletTransaction
    {
        return DB::transaction(function () use ($data) {
            $wallet = PatientWallet::findOrFail($data['WalletID']);
            $amount = (float) $data['Amount'];
            $transactionType = strtoupper($data['TransactionType']);
            
            // Calculate new balance
            $balanceBefore = (float) $wallet->Balance;
            if ($transactionType === 'DEBIT' && $amount > $balanceBefore) {
                throw new \InvalidArgumentException('Insufficient wallet balance');
            }
            $balanceAfter = $transactionType === 'CREDIT' 
                ? $balanceBefore + $amount 
                : $balanceBefore - $amount;
            
            // Create transaction
            $transaction = WalletTransaction::create([
                'TransactionID' => (string) Str::uuid(),
                'WalletID' => $wallet->WalletID,
                'PatientID' => $data['PatientID'] ?? $wallet->PatientID,
                'FamilyID' => $data['FamilyID'] ?? $wallet->FamilyID,
                'TreatmentDoneID' => $data['TreatmentDoneID'] ?? null,
                'ReceiptID' => $data['ReceiptID'] ?? null,
                'Amount' => $amount,
                'TransactionType' => $transactionType,
                'ReferenceType' => $data['ReferenceType'] ?? null,
                'ReferenceID' => $data['ReferenceID'] ?? null,
                'Description' => $data['Description'] ?? null,
                'BalanceBefore' => $balanceBefore,
                'BalanceAfter' => $balanceAfter,
                'Status' => 'COMPLETED',
                'CreatedBy' => $data['CreatedBy'] ?? null,
                'CreatedOn' => now(),
            ]);
            
            // Update wallet balance
            $wallet->update([
                'Balance' => $balanceAfter,
                'LastTransactionDate' => now(),
                'LastUpdatedOn' => now(),
                'LastUpdatedBy' => $data['CreatedBy'] ?? null,
            ]);
            
            return $transaction;
        });
    }

    /**
     * Create a credit note top-up: creates a receipt marked as credit note and a wallet CREDIT transaction.
     */
    public function createCreditNoteReceiptAndTopUp(Patient $patient, array $receiptData, float $amount, ?string $createdBy = null): array
    {
        return DB::transaction(function () use ($patient, $receiptData, $amount, $createdBy) {
            $wallet = $this->getOrCreateWalletForPatient($patient, $createdBy);

            $receipt = PatientReceipt::create(array_merge($receiptData, [
                'PatientID' => $patient->PatientID,
                'InvoiceID' => null,
                'PatientTreatmentDoneId' => null,
                'TreatmentPayment' => $amount,
                'WalletAmount' => 0,
                'IsCreditNote' => true,
                'IsWalletPayment' => false,
                'CreatedBy' => $createdBy,
                'CreatedOn' => $receiptData['CreatedOn'] ?? now(),
                'LastUpdatedBy' => $createdBy,
                'LastUpdatedOn' => $receiptData['LastUpdatedOn'] ?? now(),
            ]));

            $tx = $this->createTransaction([
                'WalletID' => $wallet->WalletID,
                'PatientID' => $patient->PatientID,
                'FamilyID' => $patient->FamilyID ?? null,
                'ReceiptID' => $receipt->ReceiptID,
                'Amount' => $amount,
                'TransactionType' => 'CREDIT',
                'ReferenceType' => 'CREDIT_NOTE',
                'ReferenceID' => $receipt->ReceiptID,
                'Description' => $receiptData['ReceiptNotes'] ?? 'Credit note top-up',
                'CreatedBy' => $createdBy,
            ]);

            $receipt->update([
                'WalletTransactionID' => $tx->TransactionID,
            ]);

            return [
                'wallet' => $wallet->fresh(['patient', 'transactions']),
                'receipt' => $receipt->fresh(),
                'transaction' => $tx,
            ];
        });
    }

    /**
     * Get wallet transactions with pagination
     */
    public function getWalletTransactions(
        string $walletId, 
        ?string $type = null, 
        int $perPage = 15
    ) {
        $query = WalletTransaction::where('WalletID', $walletId)
            ->orderBy('CreatedOn', 'desc');
            
        if ($type) {
            $query->where('TransactionType', strtoupper($type));
        }
        
        return $query->paginate($perPage);
    }

    /**
     * Get transaction by ID
     */
    public function getTransactionById(string $transactionId): ?WalletTransaction
    {
        return WalletTransaction::with('wallet')
            ->findOrFail($transactionId);
    }
}
