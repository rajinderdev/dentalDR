<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Services\EventService;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    use ApiResponse;

    protected EventService $eventService;

    public function __construct()
    {
        $this->eventService = new EventService();
    }

    /**
     * @group Event
     *
     * @method GET
     *
     * List all events
     *
     * @get /
     *
     * @queryParam startDate string optional. Filter events from this date (Y-m-d format). Example: 2025-04-08
     * @queryParam endDate string optional. Filter events until this date (Y-m-d format). Example: 2025-10-08
     * @queryParam per_page integer optional. Number of items per page. Example: 50
     *
     * @response 200 scenario="Success" {
     *     {
     *         "data": {
     *             "events": [],
     *             "pagination": {
     *                 "current_page": 1,
     *                 "per_page": 50,
     *                 "total": 100
     *             }
     *         }
     *     }
     * }
     *
     * @response 500 {"message": "Internal server error"}
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->query('per_page', env('DEFAULT_PER_PAGE', 50));
            $startDate = $request->query('startDate');
            $endDate = $request->query('endDate');

            $data = $this->eventService->getEvents($perPage, $startDate, $endDate);

            return $this->successResponse([
                'events' => EventResource::collection($data['events']),
                'pagination' => $data['pagination']
            ]);
        } catch (Exception $e) {
            Log::error('Error fetching events: ' . $e->getMessage());

            return $this->errorResponse([
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @group Event
     *
     * @method GET
     *
     * Get a single event
     *
     * @get /{id}
     *
     * @urlParam id string required. The ID of the event. Example: 123e4567-e89b-12d3-a456-426614174000
     *
     * @response 200 scenario="Success" {
     *     {
     *         "data": {
     *             "event": {}
     *         }
     *     }
     * }
     *
     * @response 404 {"message": "Event not found"}
     * @response 500 {"message": "Internal server error"}
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        try {
            $event = $this->eventService->getEventById($id);

            if (!$event) {
                return $this->errorResponse([
                    'message' => 'Event not found',
                ], 404);
            }

            return $this->successResponse([
                'event' => new EventResource($event)
            ]);
        } catch (Exception $e) {
            Log::error('Error fetching event: ' . $e->getMessage());

            return $this->errorResponse([
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @group Event
     *
     * @method POST
     *
     * Create a new event
     *
     * @post /
     *
     * @response 201 scenario="Success" {
     *     {
     *         "data": {
     *             "message": "Event created successfully",
     *             "event": {}
     *         }
     *     }
     * }
     *
     * @response 400 {"message": "Validation error", "errors": {"field": ["Error message"]}}
     * @response 500 {"message": "Internal server error"}
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(EventRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $event = $this->eventService->createEvent($validatedData);

            return $this->successResponse([
                'message' => 'Event created successfully',
                'event' => new EventResource($event),
            ], 201);
        } catch (Exception $e) {
            Log::error('Error creating event: ' . $e->getMessage());

            return $this->errorResponse([
                'message' => 'Failed to create event',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @group Event
     *
     * @method PUT
     *
     * Update an existing event
     *
     * @put /{id}
     *
     * @urlParam id string required. The ID of the event to update. Example: 123e4567-e89b-12d3-a456-426614174000
     *
     * @response 200 scenario="Success" {
     *     {
     *         "data": {
     *             "message": "Event updated successfully",
     *             "event": {}
     *         }
     *     }
     * }
     *
     * @response 400 {"message": "Validation error", "errors": {"field": ["Error message"]}}
     * @response 404 {"message": "Event not found"}
     * @response 500 {"message": "Internal server error"}
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(EventRequest $request, string $id)
    {
        try {
            $event = Event::find($id);

            if (!$event) {
                return $this->errorResponse([
                    'message' => 'Event not found',
                ], 404);
            }

            $validatedData = $request->validated();
            $updatedEvent = $this->eventService->updateEvent($event, $validatedData);

            return $this->successResponse([
                'message' => 'Event updated successfully',
                'event' => new EventResource($updatedEvent)
            ]);
        } catch (Exception $e) {
            Log::error('Error updating event: ' . $e->getMessage());

            return $this->errorResponse([
                'message' => 'Failed to update event',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @group Event
     *
     * @method DELETE
     *
     * Delete an event
     *
     * @delete /{id}
     *
     * @urlParam id string required. The ID of the event to delete. Example: 123e4567-e89b-12d3-a456-426614174000
     *
     * @response 200 scenario="Success" {
     *     {
     *         "data": {
     *             "message": "Event deleted successfully"
     *         }
     *     }
     * }
     *
     * @response 404 {"message": "Event not found"}
     * @response 500 {"message": "Internal server error"}
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        try {
            $event = Event::find($id);

            if (!$event) {
                return $this->errorResponse([
                    'message' => 'Event not found',
                ], 404);
            }

            $this->eventService->deleteEvent($event);

            return $this->successResponse([
                'message' => 'Event deleted successfully'
            ]);
        } catch (Exception $e) {
            Log::error('Error deleting event: ' . $e->getMessage());

            return $this->errorResponse([
                'message' => 'Failed to delete event',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
