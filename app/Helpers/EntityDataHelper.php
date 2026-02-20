<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\UsersClinicInfo;

class EntityDataHelper
{
    /**
     * Prepare entity data with common fields for creation
     * 
     * @param array $validatedData
     * @param object|null $authUser
     * @return array
     */
    public static function prepareForCreation(array $validatedData, $authUser = null): array
    {
        // Get authenticated user if not provided
        if (!$authUser) {
            $authUser = Auth::user();
        }

        // Get user email for tracking
        $userID = $authUser && !empty($authUser->UserID) ? $authUser->UserID : null;
        $clinicInfo = UsersClinicInfo::where('UserID', $authUser->UserID)->first();
        $clinicId = $clinicInfo ? $clinicInfo->ClinicID : '7B13B11A-9A05-4E1D-A547-7E5A2F2B3FD2';
        // Set common creation fields
        $validatedData['CreatedOn'] = $validatedData['CreatedOn'] ?? now();
        $validatedData['CreatedBy'] = $validatedData['CreatedBy'] ?? $userID;
        $validatedData['LastUpdatedBy'] = $validatedData['LastUpdatedBy'] ?? $userID;
        $validatedData['LastUpdatedOn'] = $validatedData['LastUpdatedOn'] ?? now();
        $validatedData['rowguid'] = $validatedData['rowguid'] ?? strtoupper(Str::uuid()->toString());
        // Set ClinicID if not provided or empty, and ensure user has a valid clinic
        if (!isset($validatedData['ClinicID']) || $validatedData['ClinicID'] === '') {
            if ($clinicId) {
                $validatedData['ClinicID'] = $clinicId;
            } 
        }
        return $validatedData;
    }

    /**
     * Prepare entity data with common fields for update
     * 
     * @param array $validatedData
     * @param object|null $authUser
     * @return array
     */
    public static function prepareForUpdate(array $validatedData, $authUser = null): array
    {
        // Get authenticated user if not provided
        if (!$authUser) {
            $authUser = Auth::user();
        }

        // Get user email for tracking
        $UserID = $authUser && !empty($authUser->UserID) ? $authUser->UserID : null;
        $clinicInfo = UsersClinicInfo::where('UserID', $authUser->UserID)->first();
        $clinicId = $clinicInfo ? $clinicInfo->ClinicID : null;
        
        // Set ClinicID if not provided or empty, and ensure user has a valid clinic
        if (!isset($validatedData['ClinicID']) || $validatedData['ClinicID'] === '') {
            if ($clinicId) {
                $validatedData['ClinicID'] = $clinicId;
            } 
        }
        // Set common update fields
        $validatedData['LastUpdatedBy'] = $validatedData['last_updated_by'] ?? $UserID;
        $validatedData['LastUpdatedOn'] = $validatedData['last_updated_on'] ?? now();

        return $validatedData;
    }

    /**
     * Generate a new UUID in uppercase format
     * 
     * @return string
     */
    public static function generateRowGuid(): string
    {
        return strtoupper(Str::uuid()->toString());
    }

    /**
     * Prepare invoice details data with creation data and specific fields
     * 
     * @param array $validatedData
     * @param string|null $invoiceCodePrefix
     * @param object|null $authUser
     * @return array
     */
  public static function prepareInvoiceDetailsForCreation(array $validatedData, $authUser = null): array
    {
        $authUser = $authUser ?? Auth::user();
        $userID = $authUser->UserID ?? null;
        $now = now();

        $validatedData['InvoiceCodePrefix'] = $validatedData['InvoiceCodePrefix'] ?? 'ECGP';
        $validatedData['InvoiceDate'] = $validatedData['InvoiceDate'] ?? $now;

        // ✅ Efficient invoice number generation
        if (!isset($validatedData['InvoiceNo'])) {
            $validatedData['InvoiceNo'] = self::getNextInvoiceNoFromPatientInvoices();
        }

        // ✅ Combine prefix + timestamp
        $validatedData['InvoiceNumber'] = $validatedData['InvoiceCodePrefix'] . $now->format('YmdHis');

        // Common creation fields
        return self::prepareForCreation($validatedData, $authUser);
    }

    /**
     * Efficiently get next InvoiceNo from PatientInvoices table
     * without table scan.
     */
    protected static function getNextInvoiceNoFromPatientInvoices(): int
    {
        return DB::transaction(function () {
            // Lock latest invoice to prevent duplicates in concurrency
            $lastInvoice = DB::table('PatientInvoices')
                ->select('InvoiceNo')
                ->orderByDesc('InvoiceNo')
                ->lockForUpdate()
                ->first();

            return ($lastInvoice->InvoiceNo ?? 0) + 1;
        });
    }

     public static function getClinicId($authUser = null)
    {
        $authUser = $authUser ?? Auth::user();
        $userID = $authUser->UserID ?? null;
        if (!$authUser) {
            $authUser = Auth::user();
        }

        // Get user email for tracking
        $UserID = $authUser && !empty($authUser->UserID) ? $authUser->UserID : null;
        $clinicInfo = UsersClinicInfo::where('UserID', $authUser->UserID)->first();
        $clinicId = $clinicInfo ? $clinicInfo->ClinicID : '7B13B11A-9A05-4E1D-A547-7E5A2F2B3FD2';
        return $clinicId;
    }
}
