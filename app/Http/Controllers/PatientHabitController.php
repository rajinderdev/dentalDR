<?php

namespace App\Http\Controllers;

use App\Http\Resources\PatientHabitResource;
use App\Models\Patient;
use App\Models\PatientHabit;
use App\Services\PatientHabitService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\PatientHabitRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;


class PatientHabitController extends Controller
{
    protected $patientHabitService;

    public function __construct(PatientHabitService $patientHabitService)
    {
        $this->patientHabitService = $patientHabitService;
    }

    /**
     * Display a listing of patient habits.
     */
    public function index(Patient $patient): JsonResponse
    {
        $patientHabits = $this->patientHabitService->getAllPatientHabits($patient->PatientID);
        return PatientHabitResource::collection($patientHabits)->response();
    }

    /**
     * Get patient habits by patient ID.
     */
    public function getByPatient(string $patientId): JsonResponse
    {
        $patientHabits = $this->patientHabitService->getPatientHabitsByPatient($patientId);
        return PatientHabitResource::collection($patientHabits)->response();
    }

    /**
     * Store a newly created patient habit in storage.
     */
    /**
     * Store newly created patient habits in storage.
     *
     * @param PatientHabitRequest $request
     * @param Patient $patient
     * @return JsonResponse
     */
    /**
     * Store newly created patient habits in storage.
     * Deletes all existing habits for the patient before creating new ones.
     *
     * @param PatientHabitRequest $request
     * @param Patient $patient
     * @return JsonResponse
     */
    public function store(PatientHabitRequest $request, Patient $patient): JsonResponse
    {
        // First, delete all existing habits for this patient
        $this->patientHabitService->deleteAllPatientHabits($patient->PatientID);
        
        // Then create new habits
        $validatedData = $request->validated();
        $habits = is_array($validatedData) ? $validatedData : [$validatedData];
        
        $createdHabits = [];
        foreach ($habits as $habitData) {
            $createdHabits[] = $this->patientHabitService->createPatientHabit(
                $habitData,
                $patient->PatientID
            );
        }
        
        return PatientHabitResource::collection(collect($createdHabits))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified patient habit.
     */
    public function show(Patient $patient, PatientHabit $patientHabit): JsonResponse
    {
        $patientHabit = $this->patientHabitService->getPatientHabitById($patient->PatientID, $patientHabit->PatientHabitID);
        
        return (new PatientHabitResource($patientHabit))->response();
    }

    /**
     * Update the specified patient habit in storage.
     */
    /**
     * Update the specified patient habit in storage.
     *
     * @param PatientHabitRequest $request
     * @param Patient $patient
     * @param PatientHabit $patientHabit
     * @return JsonResponse
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \InvalidArgumentException
     */
    /**
     * Update the specified patient habit(s) in storage.
     *
     * @param Request $request
     * @param Patient $patient
     * @param string|null $patientHabitId
     * @return JsonResponse
     */
    public function update(Request $request, Patient $patient): JsonResponse
    {
        $requestData = $request->all();
        
        // Check if it's a bulk update (array of items)
        if (is_array($requestData) && array_key_exists(0, $requestData)) {
            $updatedHabits = [];
            PatientHabit::where('PatientID', $patient->PatientID)->delete();
            foreach ($requestData as $habitData) {
                $updatedHabits[] = $this->patientHabitService->createPatientHabit(
                    $habitData,
                    $patient->PatientID
                );
            }
            return PatientHabitResource::collection(collect($updatedHabits))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
        }
        return response()->json(['message' => 'Update completed'], Response::HTTP_OK);
    }

    /**
     * Remove the specified patient habit from storage.
     */
    /**
     * Remove the specified patient habit(s) from storage.
     *
     * @param Request $request
     * @param Patient $patient
     * @return JsonResponse
     */
    public function destroy(Request $request, Patient $patient): JsonResponse
    {
        $habitIds = $request->all();
        
        if (!is_array($habitIds) || empty($habitIds)) {
            return response()->json([
                'message' => 'No habit IDs provided for deletion',
                'status' => 'error'
            ], Response::HTTP_BAD_REQUEST);
        }
        
        // Delete the specified habits for this patient
        $deleted = PatientHabit::where('PatientID', $patient->PatientID)
            ->whereIn('PatientHabitID', $habitIds)
            ->update(['IsDeleted'=>true]);
            
        return response()->json([
            'message' => 'Habits deleted successfully',
            'deleted_count' => $deleted
        ], Response::HTTP_OK);
    }
}
