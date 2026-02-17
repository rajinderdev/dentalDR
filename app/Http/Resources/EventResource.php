<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->EventID,
            'description' => $this->Description,
            'start_date_time' => $this->StartDateTime?->format('Y-m-d H:i:s'),
            'end_date_time' => $this->EndDateTime?->format('Y-m-d H:i:s'),
            'status' => $this->Status,
            'created_on' => $this->CreatedOn?->format('Y-m-d H:i:s'),
            'created_by' => [
                'id' => $this->CreatedBy,
                'name' => $this->creator?->Name ?? null,
            ],
            'last_updated_on' => $this->LastUpdatedOn?->format('Y-m-d H:i:s'),
            'last_updated_by' => $this->LastUpdatedBy,
        ];
    }
}
