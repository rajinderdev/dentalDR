<?php

namespace App\Services;

use App\Models\PatientPackage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Helpers\EntityDataHelper;

class PatientPackageService
{
    public function getAll($perPage = 10, $filters = [], $patientId = null)
    {
        $query = PatientPackage::query()
            ->with(['patient', 'package'])
            ->where('PatientID', $patientId)
            ->where('EndDate', '>=', now())
            ->where('IsDeleted', false);

        // Apply filters
        if (!empty($filters['patient_id'])) {
            $query->where('PatientID', $filters['patient_id']);
        }
        
        if (!empty($filters['status'])) {
            $query->where('Status', $filters['status']);
        }

        if (!empty($filters['payment_status'])) {
            $query->where('PaymentStatus', $filters['payment_status']);
        }

        return $query->paginate($perPage);
    }

    public function getById($id, $patientId = null)    
    {
        return PatientPackage::with(['patient', 'package'])
            ->where('IsDeleted', false)
            ->where('PatientID', $patientId)
            ->findOrFail($id);
    }

    public function create(array $data, $patientId = null)
    {
        $dataToPersist = EntityDataHelper::prepareForCreation($data);     
        $dataToPersist['ClinicID'] = $dataToPersist['ClinicID'] ?? Auth::user()->ClinicID;
        $dataToPersist['PatientID'] = $patientId;
        return PatientPackage::create($dataToPersist);
    }

    public function update($id, array $data, $patientId = null)
    {
        $patientPackage = PatientPackage::where('IsDeleted', false)->where('PatientID', $patientId)->findOrFail($id);
        
        $dataToPersist = EntityDataHelper::prepareForUpdate($data);   
        
        $patientPackage->update($dataToPersist);
        
        return $patientPackage->fresh(['patient', 'package']);
    }

    public function delete($id, $patientId = null)
    {
        $patientPackage = PatientPackage::where('IsDeleted', false)->where('PatientID', $patientId)->findOrFail($id);
        
        // Soft delete
        $patientPackage->update([
            'IsDeleted' => true,
            'Status' => 'cancelled',
            'LastUpdatedBy' => Auth::user()->id
        ]);
        
        return true;
    }

    public function getActivePackages($patientId = null)
    {
        $query = PatientPackage::with(['package'])
            ->where('IsDeleted', false)
            ->where('Status', 'active')
            ->where('EndDate', '>=', now())
            ->where('PaymentStatus', 'paid');

        if ($patientId) {
            $query->where('PatientID', $patientId);
        }

        return $query->get();
    }
}
