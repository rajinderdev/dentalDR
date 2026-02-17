<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PackageServiceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'PackageServiceID' => $this->PackageServiceID,
            'PackageID' => $this->PackageID,
            'TreatmentTypeID' => $this->TreatmentTypeID,
            'TreatmentName' => $this->TreatmentName,
            'QuantityLimit' => (int) $this->QuantityLimit,
            'IsDeleted' => (bool) $this->IsDeleted,
            'CreatedOn' => $this->CreatedOn,
            'CreatedBy' => $this->CreatedBy,
            'LastUpdatedOn' => $this->LastUpdatedOn,
            'LastUpdatedBy' => $this->LastUpdatedBy,
            'treatmentType' => new TreatmentTypeHierarchyResource($this->whenLoaded('treatmentTypeHierarchy')),
        ];
    }
}
