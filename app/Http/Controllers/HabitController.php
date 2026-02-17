<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\HabitRequest;
use App\Http\Resources\HabitResource;
use App\Models\Habit;
use App\Services\HabitService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;

class HabitController extends Controller
{
    protected $habitService;

    public function __construct(HabitService $habitService)
    {
        $this->habitService = $habitService;
    }

    /**
     * Display a listing of the habits.
     */
    public function index(): JsonResponse
    {
        $habits = $this->habitService->getAllHabits();
        return HabitResource::collection($habits)->response();
    }

    /**
     * Store a newly created habit in storage.
     */
    public function store(HabitRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $habit = $this->habitService->createHabit($validated);
        
        return (new HabitResource($habit))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified habit.
     */
    public function show(Habit $habit): JsonResponse
    {
        $habit = $this->habitService->getHabitById($habit->id);
        return (new HabitResource($habit))->response();
    }

    /**
     * Update the specified habit in storage.
     */
    public function update(Request $request, Habit $habit): JsonResponse
    {
        $validated = $request->all();
        $this->habitService->updateHabit($habit, $validated);
        
        return (new HabitResource($habit->refresh()))->response();
    }

    /**
     * Remove the specified habit from storage.
     */
    public function destroy(Habit $habit): JsonResponse
    {
        $this->habitService->deleteHabit($habit);
        
        return response()->json([
            'message' => 'Habit deleted successfully',
        ], Response::HTTP_OK);
    }
}
