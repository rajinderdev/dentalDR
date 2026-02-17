<?php

namespace App\Services;

use App\Models\PersonalReminderNote;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Resources\PersonalReminderNoteResource;

class PersonalReminderNoteService
{
    /**
     * Get a paginated list of Personal Reminder Notes.
     *
     * @param int $perPage
     * @return array
     */
    public function getPersonalReminderNotes(int $perPage): array
    {
        $data = PersonalReminderNote::with([
            'reminder:ReminderId,PatientID,ProviderID,StatusId',
            'reminder.patient:PatientID,Title,FirstName,LastName',
            'reminder.doctor:ProviderID,ProviderName'
        ])
        ->paginate($perPage); 
        $reminderIds = $data->pluck('ReminderId')->unique();
        $notesCounts = PersonalReminderNote::whereIn('ReminderId', $reminderIds)
            ->selectRaw('ReminderId, COUNT(*) as notes_count')
            ->groupBy('ReminderId')
            ->pluck('notes_count', 'ReminderId');

        // Add notes count to the collection
        foreach ($data as $note) {
            if ($note->reminder) {
                $note->reminder->notes_count = $notesCounts[$note->reminder->ReminderId] ?? 0;
            }
        }

        return [
            'personal_reminder_notes' => $data, // Transform the data using the resource
            'pagination' => [
                'current_page' => $data->currentPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'last_page' => $data->lastPage(),
            ]
        ];
    }

    /**
     * Create a new personal reminder note record.
     *
     * @param array $data The validated data for creating the personal reminder note
     * @return PersonalReminderNote The newly created personal reminder note model
     */
    public function createReminderNote(array $data): PersonalReminderNote
    {
        return PersonalReminderNote::create($data);
    }

    /**
     * Update an existing personal reminder note record.
     *
     * @param PersonalReminderNote $personalReminderNote The personal reminder note model to update
     * @param array $data The validated data for updating the personal reminder note
     * @return PersonalReminderNote The updated personal reminder note model
     */
    public function updateReminderNote(PersonalReminderNote $personalReminderNote, array $data): PersonalReminderNote
    {
        $personalReminderNote->update($data);
        return $personalReminderNote;
    }
}
