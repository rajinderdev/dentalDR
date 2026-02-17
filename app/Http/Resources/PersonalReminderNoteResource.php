<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PersonalReminderNoteResource extends JsonResource
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
            'personal_reminder_notes_id' => $this->PersonalReminderNotesId,
            'reminder_id' => $this->ReminderId,
            'notes_date' => $this->NotesDate,
            'notes' => $this->Notes,
            'is_deleted' => $this->IsDeleted,
            'created_by' => $this->CreatedBy,
            'created_on' => $this->CreatedOn,
            'last_updated_by' => $this->LastUpdatedBy,
            'last_updated_on' => $this->LastUpdatedOn,
            'patient_name' => optional($this->reminder)->patient?->fullName,
            'doctor_name' => optional($this->reminder)->doctor?->ProviderName,
            'status' => optional($this->reminder)->StatusId,
            'assigned_note_count' => $this->getAssignedNoteCount(),
        ];
    }
    
    /**
     * Get the assigned note count safely
     */
    private function getAssignedNoteCount()
    {
        if (!$this->reminder) {
            return 0;
        }
        
        // Check if we have the manually added notes_count
        if (isset($this->reminder->notes_count)) {
            return $this->reminder->notes_count;
        }
        
        // Check if notes relationship is loaded
        if ($this->reminder->relationLoaded('notes')) {
            return $this->reminder->notes->count();
        }
        
        return 0;
    }
}