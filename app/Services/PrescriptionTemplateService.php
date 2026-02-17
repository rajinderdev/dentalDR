<?php

namespace App\Services;

use App\Models\PrescriptionTemplate; // Assuming you have a PrescriptionTemplate model
use App\Http\Resources\PrescriptionTemplateResource; // Assuming you have a resource for Prescription Template
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Helpers\EntityDataHelper;

class PrescriptionTemplateService
{
    /**
     * Get a paginated list of Prescription Templates.
     *
     * @param int $perPage
     * @return array
     */
    public function getPrescriptionTemplates(int $perPage): array
    {
        $data = PrescriptionTemplate::where('IsDeleted', 0)->orderBy('createdOn','desc')->paginate($perPage); // Fetch paginated prescription templates

        return [
            'prescription_templates' => $data, // Transform the data using the resource
            'pagination' => [
                'current_page' => $data->currentPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'last_page' => $data->lastPage(),
            ]
        ];
    }

    /**
     * Create a new prescription template record.
     *
     * @param array $data The validated data for creating the prescription template
     * @return PrescriptionTemplate The newly created prescription template model
     */
    public function createPrescriptionTemplate(array $data): PrescriptionTemplate
    {
         $validatedData = EntityDataHelper::prepareForCreation($data); 
        return PrescriptionTemplate::create($validatedData);
    }

    /**
     * Update an existing prescription template record.
     *
     * @param PrescriptionTemplate $prescriptionTemplate The prescription template model to update
     * @param array $data The validated data for updating the prescription template
     * @return PrescriptionTemplate The updated prescription template model
     */
    public function updatePrescriptionTemplate(PrescriptionTemplate $prescriptionTemplate, array $data): PrescriptionTemplate
    {
        $validatedData = EntityDataHelper::prepareForUpdate($data);
        $prescriptionTemplate->update($validatedData);
        return $prescriptionTemplate;
    }
    
    /**
     * Delete a prescription template record.
     *
     * @param PrescriptionTemplate $prescriptionTemplate The prescription template model to delete
     * @return PrescriptionTemplate The updated prescription template model
     */
    public function deletePrescriptionTemplate(PrescriptionTemplate $prescriptionTemplate): PrescriptionTemplate
    {
        $prescriptionTemplate->update(['IsDeleted' => true]);
        return $prescriptionTemplate;
    }
}
