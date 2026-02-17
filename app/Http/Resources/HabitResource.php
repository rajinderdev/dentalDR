<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HabitResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->HabitID,
            'name' => $this->Name,
            'description' => $this->Description,
            'is_active' => $this->IsActive,
            'created_by' => $this->CreatedBy,
            'created_on' => $this->CreatedOn ? $this->CreatedOn->format('Y-m-d H:i:s') : null,
            'last_updated_by' => $this->LastUpdatedBy,
            'last_updated_on' => $this->LastUpdatedOn ? $this->LastUpdatedOn->format('Y-m-d H:i:s') : null,
        ];
    }
}
