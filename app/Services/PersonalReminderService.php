<?php

namespace App\Services;

use App\Models\PersonalReminder;
use App\Http\Resources\PersonalReminderResource;
use App\Helpers\StatusHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PersonalReminderService
{
    /**
     * Get a paginated list of Personal Reminders.
     *
     * @param int $perPage
     * @param string|null $patientId
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getPersonalReminders(int $perPage, $patientId = null, $startDate = null, $endDate = null): array
    {
        $query = PersonalReminder::where('IsDeleted', false);
        
        // Apply patient filter
        if($patientId != null) {
            $query->where('PatientID', $patientId);
        }
        
        // Apply date range filter
        if($startDate) {
            $query->whereDate('ReminderDate', '>=', $startDate);
        }
        
        if($endDate) {
            $query->whereDate('ReminderDate', '<=', $endDate);
        }
        
        // Order by reminder date (upcoming first)
        $data = $query->orderBy('ReminderDate', 'desc')->paginate($perPage);

        return [
            'personal_reminders' => PersonalReminderResource::collection($data),
            'pagination' => [
                'current_page' => $data->currentPage(),
                'total_pages' => $data->lastPage(),
                'total' => $data->total(),
            ]
        ];
    }

    /**
     * Create a new personal reminder.
     *
     * @param array $data The validated data for creating personal reminder
     * @return PersonalReminder The newly created personal reminder model
     */
    public function createPersonalReminder(array $data): PersonalReminder
    {
        // Add auto-generated fields
        $data['ReminderId'] = (string) Str::uuid();
        $data['CreatedOn'] = now();
        $data['CreatedBy'] = Auth::id();
        $data['LastUpdatedOn'] = now();
        $data['LastUpdatedBy'] = Auth::id();
        $data['rowguid'] = (string) Str::uuid();
        
        return PersonalReminder::create($data);
    }

    /**
     * Update an existing personal reminder record.
     *
     * @param PersonalReminder $personalReminder The personal reminder model to update
     * @param array $data The validated data for updating the personal reminder
     * @return PersonalReminder The updated personal reminder model
     */
    public function updatePersonalReminder(PersonalReminder $personalReminder, array $data): PersonalReminder
    {
        // Map StatusId if it's provided as a string
        if (isset($data['StatusId'])) {
            $data['StatusId'] = StatusHelper::getStatusId($data['StatusId']);
        }
        
        $personalReminder->update($data);
        return $personalReminder;
    }
}
