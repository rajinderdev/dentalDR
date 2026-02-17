<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'PackageID' => $this->PackageID,
            'ClinicID' => $this->ClinicID,
            'PackageName' => $this->PackageName,
            'PackageCode' => $this->PackageCode,
            'Description' => $this->Description,
            'Price' => (float) $this->Price,
            'Interval' => $this->Interval,
            'DiscountAmount' => (float) $this->DiscountAmount,
            'AdditionAmount' => (float) $this->AdditionAmount,
            'Status' => $this->Status,
            'IsDeleted' => (bool) $this->IsDeleted,
            'CreatedOn' => $this->CreatedOn,
            'CreatedBy' => $this->CreatedBy,
            'LastUpdatedOn' => $this->LastUpdatedOn,
            'LastUpdatedBy' => $this->LastUpdatedBy,
            // 'services' => PackageServiceResource::collection($this->whenLoaded('services')),
        ];
    }
}
