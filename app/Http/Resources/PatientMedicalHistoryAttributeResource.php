<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PatientMedicalHistoryAttributeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'PatientMedicalDetailID' => $this->PatientMedicalDetailID,
            'patientID' => $this->PatientID,
            'MedicalAttributesCategory' => $this->MedicalAttributesCategory,
            'MedicalAttributesID' => $this->MedicalAttributesID,
            'MedicalAttributeValue' => $this->MedicalAttributeValue,
            'MedicalAttributeText' => $this->MedicalAttributeText,
            'MedicalHistoryDate' => $this->MedicalHistoryDate,
            'LastUpdatedBy' => $this->LastUpdatedBy,
            'LastUpdatedOn' => $this->LastUpdatedOn,
        ];
    }
}