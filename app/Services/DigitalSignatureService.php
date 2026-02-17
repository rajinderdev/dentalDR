<?php

namespace App\Services;

use App\Models\DigitalSignature;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
    /**
     * Class DigitalSignatureService
 * 
 * @package App\Services
 */
class DigitalSignatureService
{
    /**
     * Save digital signature to database
     *
     * @param array $signatureData
     * @return \App\Models\DigitalSignature
     */
    public function saveSignature(array $signatureData)
    {
        try {
            DB::beginTransaction();
            $preparedData = [
                'PatientID' => $signatureData['PatientID'],
                'ProviderID' => $signatureData['ProviderID'],
                'signature_data' => $signatureData['signatureData'],
            ];
            
            // Filter out null values to only include non-null data
            $filteredData = array_filter($preparedData, function($value) {
                return $value !== null;
            });
            $filteredData['CreatedOn'] = now();
            $filteredData['LastUpdatedOn'] = now();
            $filteredData['signature_datetime'] = now();
            $filteredData['rowguid'] = strtoupper(Str::uuid()->toString());
            $signature = DigitalSignature::create($filteredData);
            DB::commit();
            Session::forget('PatientID');
            return $signature;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Remove patient ID from session
     *
     * @param string|null $patientId
     * @return bool
     */
    public function removePatientId($patientId = null)
    {
        try {
            if ($patientId) {
                // Remove specific patient ID from session
                Session::forget('PatientID');
                Log::info('Patient ID removed from session', ['patient_id' => $patientId]);
            } else {
                // Remove any patient ID from session
                Session::forget('PatientID');
                Log::info('All patient ID removed from session');
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error removing patient ID from session', ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    /**
     * Get signature by patient ID
     *
     * @param string $patientId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSignaturesByPatient($patientId)
    {
        return DigitalSignature::where('PatientID', $patientId)
            ->orderBy('CreatedOn', 'desc')
            ->get();
    }
    
    /**
     * Get latest signature by patient ID
     *
     * @param string $patientId
     * @return \App\Models\DigitalSignature|null
     */
    public function getLatestSignatureByPatient($patientId)
    {
        return DigitalSignature::where('PatientID', $patientId)
            ->orderBy('CreatedOn', 'desc')
            ->first();
    }
}
