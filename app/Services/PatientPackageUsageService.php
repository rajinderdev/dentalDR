<?php

namespace App\Services;

use App\Models\PatientPackageUsage;
use App\Helpers\EntityDataHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PatientPackageUsageService
{
    public function getAll($perPage = 10, $filters = [], $patientPackageId = null)
    {
        $query = PatientPackageUsage::with(['patientPackage', 'treatment', 'provider'])
            ->where('IsDeleted', false);

        if ($patientPackageId) {
            $query->where('PatientPackageID', $patientPackageId);
        }

        // Apply filters
        if (!empty($filters['treatment_id'])) {
            $query->where('PatientTreatmentDoneID', $filters['treatment_id']);
        }
        
        if (!empty($filters['provider_id'])) {
            $query->where('ProviderID', $filters['provider_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('TreatmentDate', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('TreatmentDate', '<=', $filters['date_to']);
        }

        return $query->orderBy('TreatmentDate', 'desc')->paginate($perPage);
    }

    public function getById(string $id, string $patientPackageId = null)
    {
        $query = PatientPackageUsage::with(['patientPackage', 'treatment', 'provider'])
            ->where('IsDeleted', false);

        if ($patientPackageId) {
            $query->where('PatientPackageID', $patientPackageId);
        }

        return $query->findOrFail($id);
    }

    public function create(array $data, string $patientPackageId = null)
    {
        $dataToPersist = EntityDataHelper::prepareForCreation($data);
        
        if ($patientPackageId) {
            $dataToPersist['PatientPackageID'] = $patientPackageId;
        }
        
        $dataToPersist['ClinicID'] = $dataToPersist['ClinicID'] ?? Auth::user()->ClinicID;
        $dataToPersist['CreatedBy'] = Auth::id();
        
        return PatientPackageUsage::create($dataToPersist);
    }

    public function update(string $id, array $data, string $patientPackageId = null)
    {
        $query = PatientPackageUsage::where('IsDeleted', false);
        
        if ($patientPackageId) {
            $query->where('PatientPackageID', $patientPackageId);
        }
        
        $usage = $query->findOrFail($id);
        
        $dataToUpdate = EntityDataHelper::prepareForUpdate($data);
        $dataToUpdate['LastUpdatedBy'] = Auth::id();
        
        $usage->update($dataToUpdate);
        
        return $usage->fresh(['patientPackage', 'treatment', 'provider']);
    }

    public function delete(string $id, string $patientPackageId = null): bool
    {
        $query = PatientPackageUsage::where('IsDeleted', false);
        
        if ($patientPackageId) {
            $query->where('PatientPackageID', $patientPackageId);
        }
        
        $usage = $query->findOrFail($id);
        
        $usage->update([
            'IsDeleted' => true,
            'LastUpdatedBy' => Auth::id()
        ]);
        
        return true;
    }
}
