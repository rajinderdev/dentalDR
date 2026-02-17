<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class EventService
{
    /**
     * Get all events with pagination
     *
     * @param int $perPage
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getEvents(int $perPage, ?string $startDate = null, ?string $endDate = null)
    {
        $query = Event::query();

        // Apply date range filters if provided
        if ($startDate) {
            $query->whereDate('StartDateTime', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('EndDateTime', '<=', $endDate);
        }

        $events = $query->orderBy('StartDateTime', 'desc')
            ->paginate($perPage);

        return [
            'events' => $events,
            'pagination' => [
                'current_page' => $events->currentPage(),
                'per_page' => $events->perPage(),
                'total' => $events->total(),
                'last_page' => $events->lastPage(),
            ]
        ];
    }

    /**
     * Get a single event by ID
     *
     * @param string $eventId
     * @return Event|null
     */
    public function getEventById(string $eventId)
    {
        return Event::find($eventId);
    }

    /**
     * Create a new event
     *
     * @param array $data
     * @return Event
     */
    public function createEvent(array $data): Event
    {
        $user = Auth::user();

        return DB::transaction(function () use ($data, $user) {
            $event = Event::create([
                'Description' => $data['Description'] ?? null,
                'StartDateTime' => $data['StartDateTime'],
                'EndDateTime' => $data['EndDateTime'],
                'Status' => $data['Status'] ?? 'Active',
                'CreatedBy' => $user?->UserID ?? null,
                'CreatedOn' => now(),
                'LastUpdatedBy' => $user?->UserID ?? null,
                'LastUpdatedOn' => now(),
            ]);

            return $event;
        });
    }

    /**
     * Update an existing event
     *
     * @param Event $event
     * @param array $data
     * @return Event
     */
    public function updateEvent(Event $event, array $data): Event
    {
        $user = Auth::user();

        return DB::transaction(function () use ($event, $data, $user) {
            $event->update([
                'Description' => $data['Description'] ?? $event->Description,
                'StartDateTime' => $data['StartDateTime'] ?? $event->StartDateTime,
                'EndDateTime' => $data['EndDateTime'] ?? $event->EndDateTime,
                'Status' => $data['Status'] ?? $event->Status,
                'LastUpdatedBy' => $user?->UserID ?? null,
                'LastUpdatedOn' => now(),
            ]);

            return $event->fresh();
        });
    }

    /**
     * Delete an event
     *
     * @param Event $event
     * @return bool
     */
    public function deleteEvent(Event $event): bool
    {
        return DB::transaction(function () use ($event) {
            return $event->delete();
        });
    }

    /**
     * Get events by date range
     *
     * @param string $startDate
     * @param string $endDate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getEventsByDateRange(string $startDate, string $endDate)
    {
        return Event::whereBetween('StartDateTime', [$startDate, $endDate])
            ->orderBy('StartDateTime', 'asc')
            ->get();
    }

    /**
     * Get events by status
     *
     * @param string $status
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getEventsByStatus(string $status)
    {
        return Event::where('Status', $status)
            ->orderBy('StartDateTime', 'desc')
            ->get();
    }
}
