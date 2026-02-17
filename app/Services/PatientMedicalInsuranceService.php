<?php

namespace App\Services;

use App\Models\PatientMedicalInsurance;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Helpers\EntityDataHelper;

class PatientMedicalInsuranceService
{
    

    /**
     * Get insurances by patient ID
     */
    public function getInsurancesByPatient(string $patientId): Collection
    {
        return PatientMedicalInsurance::where('PatientID', $patientId)
            ->where('IsDeleted', false)
            ->orderBy('IsActive', 'desc')
            ->orderBy('CreatedOn', 'desc')
            ->get();
    }

    /**
     * Get a specific insurance by ID
     */
    public function getInsuranceById(string $insuranceId, string $patientId): PatientMedicalInsurance
    {
        return PatientMedicalInsurance::where('PatientMedicalInsuranceID', $insuranceId)
            ->where('IsDeleted', false)
            ->where('PatientID', $patientId)
            ->firstOrFail();
    }

    /**
     * Create a new patient medical insurance
     */
    public function createInsurance(array $data, string $patientId): PatientMedicalInsurance
    {
        $dataToPersist = EntityDataHelper::prepareForCreation($data);
        $dataToPersist['PatientMedicalInsuranceID'] = (string) Str::uuid();
        $dataToPersist['PatientID'] = $patientId;
        
        return PatientMedicalInsurance::create($dataToPersist);
    }

    /**
     * Update an existing patient medical insurance
     */
    public function updateInsurance(string $PatientMedicalInsuranceID, string $patientId, array $data): PatientMedicalInsurance
    {
        $insurance = $this->getInsuranceById($PatientMedicalInsuranceID, $patientId);
        $dataToUpdate = EntityDataHelper::prepareForUpdate($data);
        
        $insurance->update($dataToUpdate);
        
        return $insurance->fresh();
    }

    /**
     * Delete a patient medical insurance (soft delete)
     */
    public function deleteInsurance(string $PatientMedicalInsuranceID, string $patientId): bool
    {
        $insurance = $this->getInsuranceById($PatientMedicalInsuranceID, $patientId);
        return $insurance->update(['IsDeleted' => true]);
    }

   
}
