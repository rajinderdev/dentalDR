<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientRelationRequest;
use App\Http\Resources\PatientRelationResource;
use App\Models\Patient;
use App\Models\PatientRelation;
use App\Services\PatientRelationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PatientRelationController extends Controller
{
    protected $patientRelationService;

    public function __construct(PatientRelationService $patientRelationService)
    {
        $this->patientRelationService = $patientRelationService;
    }

    /**
     * Display a listing of patient relations.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = $request->input('per_page', 15);
        $relations = $this->patientRelationService->getAllRelations($perPage);
        return PatientRelationResource::collection($relations);
    }

    /**
     * Get relations by patient ID.
     */
    public function getByPatient(Patient $patient): AnonymousResourceCollection
    {
        $relations = $this->patientRelationService->getRelationsByPatient($patient->PatientID);
        return PatientRelationResource::collection($relations);
    }

    /**
     * Store a newly created patient relation in storage.
     */
    public function store(PatientRelationRequest $request, Patient $patient): JsonResponse
    {
        $validated = $request->validated();
        
        // Ensure PatientID is set from the route parameter
        $validated['PatientID'] = $patient->PatientID;
        
        $relation = $this->patientRelationService->createRelation($validated);
        
        return (new PatientRelationResource($relation))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified patient relation.
     */
    public function show(Patient $patient, PatientRelation $patientRelation): PatientRelationResource
    {
        // Verify the relation belongs to the patient
        if ($patientRelation->PatientID !== $patient->PatientID) {
            abort(Response::HTTP_NOT_FOUND, 'Relation not found for this patient');
        }
        
        $relation = $this->patientRelationService->getRelationById($patientRelation->PatientRelationID);
        return new PatientRelationResource($relation);
    }

    /**
     * Update the specified patient relation in storage.
     */
    public function update(
        Request $request, 
        Patient $patient, 
        PatientRelation $patientRelation
    ): PatientRelationResource {
        // Verify the relation belongs to the patient
        if ($patientRelation->PatientID !== $patient->PatientID) {
            abort(Response::HTTP_NOT_FOUND, 'Relation not found for this patient');
        }
        
        $validated = $request->all();
        $updatedRelation = $this->patientRelationService->updateRelation($patientRelation, $validated);
        
        return new PatientRelationResource($updatedRelation);
    }

    /**
     * Remove the specified patient relation from storage.
     */
    public function destroy(Patient $patient, PatientRelation $patientRelation): JsonResponse
    {
        // Verify the relation belongs to the patient
        if ($patientRelation->PatientID !== $patient->PatientID) {
            abort(Response::HTTP_NOT_FOUND, 'Relation not found for this patient');
        }
        
        $this->patientRelationService->deleteRelation($patientRelation);
        
        return response()->json([
            'message' => 'Patient relation deleted successfully',
        ], Response::HTTP_OK);
    }
}
