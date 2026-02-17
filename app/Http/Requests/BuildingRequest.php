<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BuildingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $buildingId = $this->route('building') ? $this->route('building')->id : null;

        return [
            'building_name' => 'required|string|max:255',
            'building_code' => 'required|string|max:50|unique:buildings,building_code,' . $buildingId . ',id,IsDeleted,0',
            'address1' => 'nullable|string',
            'address2' => 'nullable|string',
            'area' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:20',
            'status' => 'boolean'
        ];
    }

    public function attributes()
    {
        return [
            'building_name' => 'Building Name',
            'building_code' => 'Building Code',
            'address1' => 'Address Line 1',
            'address2' => 'Address Line 2',
            'area' => 'Area',
            'city' => 'City',
            'state' => 'State',
            'country' => 'Country',
            'pincode' => 'Pincode',
            'status' => 'Status'
        ];
    }
}
