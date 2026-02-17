<?php

namespace App\Services;

use App\Models\PatientPrescription;
use App\Models\PatientInvestigation;
use App\Models\PatientDrugsPrescription;
use App\Models\Drug;
use App\Http\Resources\PatientPrescriptionResource;
use App\Models\Patient;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Helpers\EntityDataHelper;
use Exception;

class PatientPrescriptionService
{
    /**
     * Get a paginated list of Patient Prescriptions.
     *
     * @param int $perPage
     * @return array
     */
    public function getPatientPrescriptions($patient=null, int $perPage): array
    {
        $data = PatientPrescription::where('PatientID', $patient)->where('IsDeleted', false)
            ->with(['patient_drugs_prescriptions', 'patient_investigation'])->orderBy('CreatedOn', 'desc')// Load medicines and investigation relationships
            ->paginate($perPage);

        return [
            'patient_prescriptions' => $data, // Transform the data using the resource
            'pagination' => [
                'current_page' => $data->currentPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'last_page' => $data->lastPage()
            ]
        ];
    }

    /**
     * Create a comprehensive prescription with investigation and medicines.
     *
     * @param array $data The validated data for creating the patient prescription
     * @return PatientPrescription The newly created patient prescription model
     */
    public function createPrescription(array $data): PatientPrescription
    {
        return DB::transaction(function () use ($data) {
            // Create Patient Investigation if investigation data is provided
            $investigationId = null;
            if ($this->hasInvestigationData($data)) {
                $investigation = $this->createPatientInvestigation($data);
                $investigationId = $investigation->PatientInvestigationID;
            }
            
            // Prepare prescription data
            $prescriptionData = [
                'PatientID' => $data['PatientID'],
                'ProviderID' => $data['ProviderID'] ?? $data['doctor'] ?? null,
                'DateOfPrescription' => $data['DateOfPrescription'],
                'PrescriptionNote' => $data['PrescriptionNote'] ?? null,
                'NextFollowUp' => $data['NextFollowUp'] ?? null,
                'PatientInvestigationID' => $investigationId,
                'IsFolloupSMSRequired' => $data['notifyFollowUp'] ?? false,
                'InvestigationAdvisedIDCSV' => $data['InvestigationAdvisedIDCSV'] ?? null,
                'IsDeleted' => false
            ];
            
            $prescriptionData = EntityDataHelper::prepareForCreation($prescriptionData);
            $prescription = PatientPrescription::create($prescriptionData);
            
            // Create medicines from template
            if (!empty($data['rxTemplate']['templates'])) {
                $this->createMedicinesFromTemplate($prescription->PatientPrescriptionID, $data['rxTemplate']['templates']);
            }
            
            // Create custom medicines
            if (!empty($data['medicines'])) {
                $this->createCustomMedicines($prescription->PatientPrescriptionID, $data['medicines']);
            }
            
            return $prescription->load(['patient_drugs_prescriptions', 'patient_investigation']);
        });
    }

    /**
     * Update an existing patient prescription record.
     *
     * @param PatientPrescription $patientPrescription The patient prescription model to update
     * @param array $data The validated data for updating the patient prescription
     * @return PatientPrescription The updated patient prescription model
     */
    public function updatePrescription(PatientPrescription $patientPrescription, array $data): PatientPrescription
    {
        $patientPrescription->update($data);
        return $patientPrescription;
    }
    
    /**
     * Check if investigation data is provided
     */
    private function hasInvestigationData(array $data): bool
    {
        $investigationFields = ['weight', 'bp', 'fb', 'plsb', 'hdac', 'ldl', 'acr', 'retina', 'urine'];
        
        foreach ($investigationFields as $field) {
            if (!empty($data[$field])) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Create patient investigation record
     */
    private function createPatientInvestigation(array $data): PatientInvestigation
    {
        $investigationData = [
            'PatientID' => $data['PatientID'],
            'DateOfInvestigation' => $data['DateOfPrescription'],
            'Weight' => $data['weight'] ?? null,
            'BloodPressure' => $data['bp'] ?? null,
            'FBS' => $data['fb'] ?? null,
            'PLBS' => $data['plsb'] ?? null,
            'HbAC' => $data['hdac'] ?? null,
            'LDL' => $data['ldl'] ?? null,
            'ACR' => $data['acr'] ?? null,
            'Retina' => $data['retina'] ?? null,
            'Urine' => $data['urine'] ?? null,
            'IsDeleted' => false
        ];
        
        $investigationData = EntityDataHelper::prepareForCreation($investigationData);
        return PatientInvestigation::create($investigationData);
    }
    
    /**
     * Create medicines from prescription template
     */
    private function createMedicinesFromTemplate(string $prescriptionId, array $templates): void
    {
        foreach ($templates as $template) {
            // Skip deleted template items
            if (isset($template['is_deleted']) && $template['is_deleted']) {
                continue;
            }
            
            // Find or create drug by medicine_id or medicine_name
            $drugId = $this->findOrCreateDrug($template);
            if ($drugId) {
                $drugPrescriptionData = [
                    'PatientPrescriptionID' => $prescriptionId,
                    'DrugID' => $drugId,
                    'FrequencyID' => $template['frequency_id'] ?? 1,
                    'DosageID' => $template['dosage'] ?? null,
                    'Duration' => $template['duration'] ?? null,
                    'DrugNote' => $template['drug_note'] ?? null
                ];
                $drugPrescriptionData = EntityDataHelper::prepareForCreation($drugPrescriptionData);
                PatientDrugsPrescription::create($drugPrescriptionData);
            }
        }
    }
    
    /**
     * Create custom medicines
     */
    private function createCustomMedicines(string $prescriptionId, array $medicines): void
    {
        foreach ($medicines as $medicine) {
            if (empty($medicine['medicine_name'])) {
                continue;
            }
            
            // Find or create drug
            $drugId = $this->findOrCreateDrug([
                'medicine_name' => $medicine['medicine_name']
            ]);
            
            if ($drugId) {
                $drugPrescriptionData = [
                    'FrequencyID' => $template['frequency_id'] ?? 1,
                    'PatientPrescriptionID' => $prescriptionId,
                    'DrugID' => $drugId,
                    'FrequencyID' => $medicine['frequency'] ?? null,
                    'DosageID' => $medicine['dosage'] ?? null,
                    'Duration' => $medicine['duration'] ?? null,
                    'DrugNote' => $medicine['drug_note'] ?? null
                ];
                $drugPrescriptionData = EntityDataHelper::prepareForCreation($drugPrescriptionData);
                PatientDrugsPrescription::create($drugPrescriptionData);
            }
        }
    }
    
    /**
     * Find existing drug or create new one
     */
    private function findOrCreateDrug(array $medicineData): ?string
    {
        // First try to find by medicine_id if provided
        if (!empty($medicineData['medicine_id'])) {
            $drug = Drug::where('DrugId', $medicineData['medicine_id'])->first();
            if ($drug) {
                return $drug->DrugId;
            }
        }
        
        // Try to find by medicine name
        $medicineName = $medicineData['medicine_name'] ?? null;
        if ($medicineName) {
            $drug = Drug::where('GenericName', 'LIKE', '%' . $medicineName . '%')->first();
            if ($drug) {
                return $drug->DrugId;
            }
            
            // Create new drug if not found
            try {
                $drugData = EntityDataHelper::prepareForCreation([
                    'GenericName' => $medicineName,
                    'IsDeleted' => false
                ]);
                
                $newDrug = Drug::create($drugData);
                return $newDrug->DrugId;
            } catch (Exception $e) {
                Log::error('Failed to create drug: ' . $e->getMessage());
                return null;
            }
        }
        
        return null;
    }
     /**
     * Get a paginated list of Patient Prescriptions.
     *
     * @param int $perPage
     * @return array
     */
    public function getPatientPrescriptionById($patientId, $id): PatientPrescription
    {
       $data = PatientPrescription::where('PatientPrescriptionID', $id)
            ->with(['patient_drugs_prescriptions', 'patient_investigation'])
            ->firstOrFail();
            return $data;
    }
     /**
     * Delete a patient prescription record.
     *
     * @param PatientPrescription $patientPrescription The patient prescription model to delete
     * @return PatientPrescription The updated patient prescription model
     */
    public function deletePatientPrescription($id): PatientPrescription
    {
        $patientPrescription = PatientPrescription::where('PatientPrescriptionID', $id)->firstOrFail();
        $patientPrescription->update(['IsDeleted' => true]);
        return $patientPrescription;
    }
}