<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DigitalSignatureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->DigitalSignaturesID,
            'patient_id' => $this->PatientID,
            'provider_id' => $this->ProviderID,
            'signature_data' => $this->signature_data,
            'signature_datetime' => $this->signature_datetime,
            'created_on' => $this->CreatedOn,
            'created_by' => $this->CreatedBy,
            'last_updated_on' => $this->LastUpdatedOn,
            'last_updated_by' => $this->LastUpdatedBy,
            'rowguid' => $this->rowguid,
        ];
    }
}
