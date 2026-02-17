<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientHabitResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->PatientHabitID,
            'patient_id' => $this->PatientID,
            'habit_id' => $this->HabitID,
            'habit_name' => $this->whenLoaded('habit', function () {
                return $this->habit->Name;
            }),
            'notes' => $this->Notes,
            'is_active' => $this->IsActive,
            'created_by' => $this->CreatedBy,
            'created_on' => $this->CreatedOn?->format('Y-m-d H:i:s'),
            'last_updated_by' => $this->LastUpdatedBy,
            'last_updated_on' => $this->LastUpdatedOn?->format('Y-m-d H:i:s'),
        ];
    }
}
