<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FamilyResource extends JsonResource
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
            'id' => $this->FamilyID, // Assuming there's a 'FamilyID' field
            'name' => $this->FamilyName, // Assuming there's a 'Name' field
            'address' => $this->getFullAddress(), // Combined address from all address fields
            'phone' => $this->Phone, // Assuming there's a 'Phone' field
            // Add other fields as necessary
        ];
    }
    
    /**
     * Get the full address by combining all address-related fields
     */
    private function getFullAddress()
    {
        $addressParts = array_filter([
            $this->AddressLine1,
            $this->AddressLine2,
            $this->Street,
            $this->Area,
            $this->City,
            $this->State,
            $this->Country,
            $this->ZipCode
        ]);
        
        return !empty($addressParts) ? implode(', ', $addressParts) : null;
    }
}