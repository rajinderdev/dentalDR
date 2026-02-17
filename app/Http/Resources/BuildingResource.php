<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BuildingResource extends JsonResource
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
            'id' => $this->id,
            'building_name' => $this->building_name,
            'building_code' => $this->building_code,
            'address1' => $this->address1,
            'address2' => $this->address2,
            'area' => $this->area,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'pincode' => $this->pincode,
            'status' => $this->status,
            'IsDeleted' => $this->IsDeleted,
            'CreatedOn' => $this->formatted_created_on,
            'CreatedBy' => $this->CreatedBy,
            'LastUpdatedOn' => $this->formatted_last_updated_on,
            'LastUpdatedBy' => $this->LastUpdatedBy
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request)
    {
        return [
            'success' => true,
            'message' => 'Operation successful'
        ];
    }
}
