<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\LookUpService;

class ClinicLabWorkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // Get all model attributes as an array
        $attributes = $this->resource->toArray();
        
        // Add relationships and computed properties
        $additionalData = [
            'patient' => $this->patient ? $this->patient->full_name : null,
            'patient_email' => $this->patient ? $this->patient->EmailAddress1 : null,
            'patient_image' => $this->patient && $this->patient->ImagePath ? asset($this->patient->ImagePath) : null,
            'provider' => $this->provider ? $this->provider->ProviderName : null,
            'lab_work_type' => $this->LabWorkType,
            'lab_work_date' => $this->LabWorkDate,
            'order_type' => $this->OrderType,
            'stage' => $this->StageID,
            'selected_teeth' => $this->SelectedTeeth,
            'total_cost' => $this->TotalCost,
            'instructions' => $this->Instructions,
            'status' => $this->LabStatus,
            'invoice_date' => $this->LabInvoiceDate,
            'invoice_number' => $this->LabInvoiceNumber,
            'sent_date' => $this->SentDate,
            'delivery_date' => $this->DeliveryDate,
            'order_number' => $this->OrderNumber,
            'collar_metal_designs' => $this->CollarMetalDesignsIDCSV,
            'pontic' => $this->PonticDesignsIDCSV,
            'treatmentId' => $this->TreatmentID,
            'sent' => $this->SentRecievedIDCSV,
            'warranty_details' => $this->WarrantyDetails,
            
            // Lab components with their details
            // Lab components with their details
            'lab_components' => $this->clinic_lab_work_details instanceof \Illuminate\Support\Collection
                ? $this->clinic_lab_work_details->groupBy(function($detail) {
                    return $detail->component->ComponentCategoryID ?? 'uncategorized';
                })->map(function($components, $categoryId) {
                    $category = \App\Models\LookUp::where('id', $categoryId)
                        ->where('IsDeleted', 0)
                        ->first();
                        
                    return [
                        'id' => $categoryId,
                        'item_title' => $category ? $category->ItemTitle : 'Uncategorized',
                        'children' => $components->map(function($detail) {
                            return [
                                'component_name' => $detail->component ? $detail->component->ComponentName : null,
                                'component_description' => $detail->SelectedTeeth,
                                'selected' => true,
                                'id' => $detail->LabWorkDetailID,
                                'LabWorkComponentID' => $detail->LabWorkComponentID ?? null,
                                'teeth' => $detail->SelectedTeeth,
                                'cost' => $detail->LabWorkComponentCost
                            ];
                        })->values()->toArray()
                    ];
                })->values()
                : (is_array($this->clinic_lab_work_details)
                    ? collect($this->clinic_lab_work_details)
                        ->groupBy(function($detail) {
                            return $detail['component']['ComponentCategoryID'] ?? 'uncategorized';
                        })
                        ->map(function($components, $categoryId) {
                            $category = \App\Models\LookUp::where('id', $categoryId)
                                ->where('IsDeleted', 0)
                                ->first();
                                
                            return [
                                'id' => $categoryId,
                                'item_title' => $category ? $category->ItemTitle : 'Uncategorized',
                                'children' => $components->map(function($detail) {
                                    return [
                                        'component_name' => $detail['component']['ComponentName'] ?? null,
                                        'component_description' => $detail['SelectedTeeth'] ?? null,
                                        'selected' => true,
                                        'id' => $detail['LabWorkDetailID'] ?? null,
                                        'LabWorkComponentID' => $detail['LabWorkComponentID'] ?? null,
                                        'teeth' => $detail['SelectedTeeth'] ?? null,
                                        'cost' => $detail['LabWorkComponentCost'] ?? null
                                    ];
                                })->values()->toArray()
                            ];
                        })->values()
                        ->toArray()
                    : []
                ),
            'lab_supplier' => $this->lab ? $this->lab->SupplierName : null,
            'lab_item_sent' => $this->SentRecievedIDCSV,
            'lab_item_received' => $this->ReceivedRecievedIDCSV,
            'lab_item_returned' => $this->ReturnedRecievedIDCSV,
            'lab_item_returned_date' => $this->ReturnedDate ? date('Y-m-d H:i:s', strtotime($this->ReturnedDate)) : null,
            'lab_item_returned_reason' => $this->ReturnedReason ?? null
        ];
        
        // Merge all attributes and additional data
        return array_merge($attributes, $additionalData);
    }  
}