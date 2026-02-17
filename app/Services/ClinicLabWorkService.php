<?php

namespace App\Services;

use App\Models\ClinicLabWork;
use App\Models\Patient;
use App\Helpers\EntityDataHelper;

class ClinicLabWorkService
{
    /**
     * Get lab works with pagination.
     *
     * @param int $perPage Number of items per page
     * @param mixed $patientId Patient ID filter
     * @param string|null $startDate Start date filter
     * @param string|null $endDate End date filter
     * @param int $currentPage Current page number
     * @return array Lab works data with pagination info
     */
    public function getLabWorks(int $perPage, $patientId, $startDate = null, $endDate = null, int $currentPage = 1,$search=null,$status=null)
    {
        $query = ClinicLabWork::query()->where('IsDeleted', false);

        // Apply patient filter
        if($patientId != null) {
            $query->where('PatientID', $patientId);
        }

        // Apply search filter by patient name
        if($search) {
            $query->whereHas('patient', function($q) use ($search) {
                $q->where(function($subQuery) use ($search) {
                    $subQuery->where('FirstName', 'LIKE', '%' . $search . '%')
                           ->orWhere('LastName', 'LIKE', '%' . $search . '%')
                           ->orWhereRaw("CONCAT(COALESCE(Title,''), ' ', FirstName, ' ', LastName) LIKE ?", ['%' . $search . '%'])
                           ->orWhereRaw("CONCAT(FirstName, ' ', LastName) LIKE ?", ['%' . $search . '%']);
                });
            });
        }
    

        // Apply date range filter
        if($startDate) {
            $query->whereDate('LabWorkDate', '>=', $startDate);
        }
        if($endDate) {
            $query->whereDate('LabWorkDate', '<=', $endDate);
        }
         if($status) {
            $query->where('LabStatus', $status);
        }
        // Order by CreatedOn descending (latest first)
        $query->orderBy('CreatedOn', 'desc');

       

        $clinicLabWork = $query->paginate($perPage, ['*'], 'page', $currentPage);

        return [
            'lab_works' => $clinicLabWork,
            'pagination' => [
                'total' => $clinicLabWork->total(),
                'per_page' => $clinicLabWork->perPage(),
                'current_page' => $clinicLabWork->currentPage(),
            ]
        ];
    }

    /**
     * Create a new lab work record.
     *
     * @param array $data The validated data for creating the lab work
     * @return ClinicLabWork The newly created lab work model
     */
    // public function createLabWork(array $data): ClinicLabWork
    // {
    //     // Convert arrays to CSV or JSON as needed for DB
    //     $sentCSV = (isset($data['sent']) && is_array($data['sent']) && !empty($data['sent'])) 
    //         ? implode(',', $data['sent']) 
    //         : null;
        
    //     $collarCSV = (isset($data['collar']) && is_array($data['collar']) && !empty($data['collar'])) 
    //         ? implode(',', $data['collar']) 
    //         : null;
        
    //     $lastRecord = ClinicLabWork::orderBy('OrderNo', 'desc')->first();
    //     $orderNo = $lastRecord ? $lastRecord->OrderNo + 1 : 1;
        
    //     $labWork = ClinicLabWork::create([
    //         'ClinicID' => !empty($data['ClinicID']) ? $data['ClinicID'] : null,
    //         'TreatmentID' => $data['treatmentId'] ?? null,
    //         'OrderNo' => $orderNo,
    //         'OrderNumber' => "LAB$orderNo",
    //         'ProviderID' => $data['inChargeLT'] ?? null,
    //         'PatientID' => $data['PatientID'] ?? null,
    //         'LabWorkDate' => $data['date'] ?? null,
    //         'LabSupplierID' => $data['supplier'] ?? null,
    //         'DeliveryDate' => $data['deliveryDate'] ?? null,
    //         'OrderType' => $data['caseType'] ?? null,
    //         'StageID' => $data['Stage'] ?? null,
    //         'SentRecievedIDCSV' => $sentCSV,
    //         'Shade' => $data['Shade'] ?? null,
    //         'SelectedTeeth' => !empty($data['selected_teeth']) ? $data['selected_teeth'] : null,
    //         'TotalCost' => $data['totalCost'] ?? null,
    //         'Instructions' => $data['instruction'] ?? null,
    //         'CollarMetalDesignsIDCSV' => $collarCSV,
    //         'LabInvoiceNumber' => !empty($data['labinvoiceno']) ? $data['labinvoiceno'] : null,
    //         'LabInvoiceDate' => $data['invoiceDate'] ?? null,
    //         'Status' => $data['status'] ?? null,
    //         'WarrantyDetails' => $data['details'] ?? null,
    //     ]);

    //     // Save lab work details (components)
    //     if (!empty($data['labComponents'])) {
    //         foreach ($data['labComponents'] as $component) {
    //             if (!empty($component['children'])) {
    //                 foreach ($component['children'] as $child) {
    //                     if($child['selected'] !== true) {
    //                         continue; // Skip if the component is not selected
    //                     }
    //                     $labWork->clinic_lab_work_details()->create([
    //                         'LabWorkComponentID' => $child['id'],
    //                         'SelectedTeeth' => $child['teeth'] ?? null,
    //                         'LabWorkComponentCost' => $child['cost'],
    //                     ]);
    //                 }
    //             }
    //         }
    //     }

    //     // Load the relationship with component details
    //     $labWork->load(['clinic_lab_work_details.component']);

    //     return $labWork;
    // }
    public function createLabWork(array $data): ClinicLabWork
{
    // Convert arrays to CSV or JSON as needed for DB
    $sentCSV = (isset($data['sent']) && is_array($data['sent']) && !empty($data['sent'])) 
        ? implode(',', $data['sent']) 
        : null;
    
    $collarCSV = (isset($data['collar']) && is_array($data['collar']) && !empty($data['collar'])) 
        ? implode(',', $data['collar']) 
        : null;
    $ponticDesignsCSV = (isset($data['pontic']) && is_array($data['pontic']) && !empty($data['pontic'])) 
        ? implode(',', $data['pontic']) 
        : null;
   
    $lastRecord = ClinicLabWork::orderBy('OrderNo', 'desc')->first();
    $orderNo = $lastRecord ? $lastRecord->OrderNo + 1 : 1;
   
    // Map the data to the database columns
            $labWorkData = [
            'ClinicID' => $data['ClinicID'] ?? null,
            'OrderNo' => $orderNo,
            'OrderNumber' => "LAB$orderNo",
            'ProviderID' => $data['inChargeLT'] ?? $data['ProviderID'] ?? null,
            'PatientID' => $data['PatientID'] ?? null,
            'LabWorkDate' => $data['date'] ?? now(),
            'LabSupplierID' => $data['supplier'] ?? null,
            'DeliveryDate' => $data['deliveryDate'] ?? null,
            'OrderType' => $data['caseType'] ?? null,
            'ParentLabWorkID' => $data['ParentLabWorkID'] ?? null, // Added missing field
            'StageID' => $data['Stage'] ?? null,
            'SentRecievedIDCSV' => $sentCSV,
            'Shade' => $data['Shade'] ?? null,
            'SelectedTeeth' => $data['selected_teeth'] ?? null,
            'PonticDesignsIDCSV' => $ponticDesignsCSV, // Added missing field
            'CollarMetalDesignsIDCSV' => $collarCSV,
            'TotalCost' => $data['totalCost'] ?? 0,
            'Instructions' => $data['instruction'] ?? $data['Instructions'] ?? null,
            'IsDeleted' => false,
            'LabStatus' => $data['status'] ?? 1,
            'WarrantyDetails' => $data['details'] ?? null,
            'LabInvoiceDate' => $data['invoiceDate'] ?? null,
            'LabInvoiceNumber' => $data['labinvoiceno'] ?? null,
            'TreatmentID' => $data['TreatmentID'] ?? null,
            'SentDate' => $data['deliveryDate'] ?? null,
            'LabWorkType'=>$data['caseType'] ?? null,
        ];
      $dataToPersist = EntityDataHelper::prepareForCreation($labWorkData);

    // Create the lab work
    $labWork = ClinicLabWork::create($dataToPersist);

    // Save lab work details (components)
    if (!empty($data['labComponents'])) {
        foreach ($data['labComponents'] as $component) {
            if (!empty($component['children'])) {
                foreach ($component['children'] as $child) {
                    if (($child['selected'] ?? false) !== true) {
                        continue; // Skip if the component is not selected
                    }
                    
                    // Create lab work detail for each selected component
                    $detail = [
                        'LabWorkComponentID' => $child['id'] ?? null,
                        'LabWorkID' => $labWork->LabWorkID,
                        'SelectedTeeth' => $child['teeth'] ?? null,
                        'LabWorkComponentCost' => $child['cost'] ?? 0,
                        'CreatedOn' => now(),
                        'LastUpdatedOn' => now(),
                        'IsDeleted' => false
                    ];
                     $detailData = EntityDataHelper::prepareForCreation($detail);
                    $labWork->clinic_lab_work_details()->create($detailData);
                }
            }
        }
    }
  // Save sent items if any
    // if (!empty($data['sent']) && is_array($data['sent'])) {
    //     $sentItems = [];
    //     $now = now();
        
    //     foreach ($data['sent'] as $sentId) {
    //         $data1= EntityDataHelper::prepareForCreation([]);
    //         $sentItems[]= [
    //             'LabWorkSentID' => (string) \Illuminate\Support\Str::uuid(),
    //             'LabWorkID' => $labWork->LabWorkID,
    //             'SentID' => $sentId,
    //             'SentDate' => $data['date'] ?? $now,
    //             'IsDeleted' => false,
    //             'CreatedBy' => $data1['CreatedBy'] ?? null,
    //             'CreatedOn' => $now,
    //             'LastUpdatedOn' => $now,
    //             'LastUpdatedBy' => $data1['LastUpdatedBy'] ?? null,
    //         ];
    //     }
        
    //     if (!empty($sentItems)) {
    //         \App\Models\ClinicLabWorkSent::insert($sentItems);
    //     }
    // }
  

    // Load the relationship with component details and sent items
    return $labWork->load(['clinic_lab_work_details.component']);
}

    /**
     * Update an existing lab work record.
     *
     * @param ClinicLabWork $clinicLabWork The lab work model to update
     * @param array $data The validated data for updating the lab work
     * @return ClinicLabWork The updated lab work model
     */
    // public function updateLabWork(ClinicLabWork $clinicLabWork, array $data): ClinicLabWork
    // {
    //     $clinicLabWork->update($data);
    //     return $clinicLabWork;
    // }

    /**
 * Update an existing lab work record.
 *
 * @param string $labWorkId
 * @param array $data
 * @return ClinicLabWork
 */
public function updateLabWork(string $labWorkId, array $data): ClinicLabWork
{
    // Find the existing lab work
    $labWork = ClinicLabWork::findOrFail($labWorkId);

    // Convert arrays to CSV or JSON as needed for DB
    $sentCSV = (isset($data['sent']) && is_array($data['sent']) && !empty($data['sent'])) 
        ? implode(',', $data['sent']) 
        : null;
    
    $collarCSV = (isset($data['collar']) && is_array($data['collar']) && !empty($data['collar'])) 
        ? implode(',', $data['collar']) 
        : null;
    $ponticDesignsCSV = (isset($data['pontic']) && is_array($data['pontic']) && !empty($data['pontic'])) 
        ? implode(',', $data['pontic']) 
        : null;
    
    // Map the data to the database columns
    $labWorkData = [
        'ClinicID' => $data['ClinicID'] ?? $labWork->ClinicID,
        'ProviderID' => $data['inChargeLT'] ?? $data['ProviderID'] ?? $labWork->ProviderID,
        'PatientID' => $data['PatientID'] ?? $labWork->PatientID,
        'LabWorkDate' => $data['date'] ?? $labWork->LabWorkDate,
        'LabSupplierID' => $data['supplier'] ?? $labWork->LabSupplierID,
        'DeliveryDate' => $data['deliveryDate'] ?? $labWork->DeliveryDate,
        'OrderType' => $data['caseType'] ?? $labWork->OrderType,
        'StageID' => $data['Stage'] ?? $labWork->StageID,
        'SentRecievedIDCSV' => $sentCSV ?? $labWork->SentRecievedIDCSV,
        'Shade' => $data['Shade'] ?? $labWork->Shade,
        'SelectedTeeth' => $data['selected_teeth'] ?? $labWork->SelectedTeeth,
        'PonticDesignsIDCSV' => $ponticDesignsCSV ?? $labWork->PonticDesignsIDCSV,
        'CollarMetalDesignsIDCSV' => $collarCSV ?? $labWork->CollarMetalDesignsIDCSV,
        'TotalCost' => $data['totalCost'] ?? $labWork->TotalCost,
        'Instructions' => $data['instruction'] ?? $data['Instructions'] ?? $labWork->Instructions,
        'LabStatus' => $data['status'] ?? $labWork->LabStatus,
        'WarrantyDetails' => $data['details'] ?? $labWork->WarrantyDetails,
        'LabInvoiceDate' => $data['invoiceDate'] ?? $labWork->LabInvoiceDate,
        'LabInvoiceNumber' => $data['labinvoiceno'] ?? $labWork->LabInvoiceNumber,
        'TreatmentID' => $data['TreatmentID'] ?? $labWork->TreatmentID,
        'SentDate' => $data['deliveryDate'] ?? $labWork->SentDate,
        'LabWorkType' => $data['caseType'] ?? $labWork->LabWorkType,
        'LastUpdatedOn' => now(),
    ];

    // Update the lab work
    $labWork->update(EntityDataHelper::prepareForUpdate($labWorkData, $labWork));

    // Update lab work details (components)
    if (isset($data['labComponents'])) {
        // Delete existing details to replace them
        $labWork->clinic_lab_work_details()->delete();

        if (!empty($data['labComponents'])) {
            foreach ($data['labComponents'] as $component) {
                if (!empty($component['children'])) {
                    foreach ($component['children'] as $child) {
                        if (($child['selected'] ?? false) !== true) {
                            continue; // Skip if the component is not selected
                        }
                        
                        // Create lab work detail for each selected component
                        $detail = [
                            'LabWorkComponentID' => $child['id'] ?? null,
                            'LabWorkID' => $labWork->LabWorkID,
                            'SelectedTeeth' => $child['teeth'] ?? null,
                            'LabWorkComponentCost' => $child['cost'] ?? 0,
                            'CreatedOn' => now(),
                            'LastUpdatedOn' => now(),
                            'IsDeleted' => false
                        ];
                        $detailData = EntityDataHelper::prepareForCreation($detail);
                        $labWork->clinic_lab_work_details()->create($detailData);
                    }
                }
            }
        }
    }

    // // Update sent items if provided
    // if (array_key_exists('sent', $data)) {
    //     // Delete existing sent items to replace them
    //     $labWork->sent_items()->delete();

    //     if (!empty($data['sent']) && is_array($data['sent'])) {
    //         $sentItems = [];
    //         $now = now();
            
    //         foreach ($data['sent'] as $sentId) {
    //             $data1 = EntityDataHelper::prepareForCreation([]);
    //             $sentItems[] = [
    //                 'LabWorkSentID' => (string) \Illuminate\Support\Str::uuid(),
    //                 'LabWorkID' => $labWork->LabWorkID,
    //                 'SentID' => $sentId,
    //                 'SentDate' => $data['date'] ?? $now,
    //                 'IsDeleted' => false,
    //                 'CreatedBy' => $data1['CreatedBy'] ?? null,
    //                 'CreatedOn' => $now,
    //                 'LastUpdatedOn' => $now,
    //                 'LastUpdatedBy' => $data1['LastUpdatedBy'] ?? null,
    //             ];
    //         }
            
    //         if (!empty($sentItems)) {
    //             \App\Models\ClinicLabWorkSent::insert($sentItems);
    //         }
    //     }
    // }

    // Refresh the model to get the latest data with relationships
    return $labWork->load(['clinic_lab_work_details.component']);
}

public function deleteLabWork(ClinicLabWork $clinicLabWork, array $data): ClinicLabWork
{
    $clinicLabWork->update([
        'IsDeleted' => true,
    ]);
    return $clinicLabWork;
}
}
