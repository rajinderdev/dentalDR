<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\PatientPackageRequest;
use App\Http\Resources\PatientPackageResource;
use App\Services\PatientPackageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Models\Patient;
use Exception;
use Illuminate\Support\Facades\Log;

class PatientPackageController extends Controller
{
    protected $patientPackageService;

    public function __construct(PatientPackageService $patientPackageService)
    {
        $this->patientPackageService = $patientPackageService;
    }

    public function index(Request $request, Patient $patient)
    {
            $perPage = $request->input('per_page', 10);
            $filters = $request->only(['status', 'payment_status']);
            $packages = $this->patientPackageService->getAll($perPage, $filters, $patient->id);
            return PatientPackageResource::collection($packages);
       
    }

    public function store(PatientPackageRequest $request, Patient $patient): JsonResponse
    {
        $package = $this->patientPackageService->create($request->validated(), $patient->id);
        
        return response()->json([
            'message' => 'Patient package created successfully',
            'data' => new PatientPackageResource($package->load(['patient', 'package']))
        ], 201);
    }

    public function show(string $id, Patient $patient): PatientPackageResource
    {
        $package = $this->patientPackageService->getById($id);
        return new PatientPackageResource($package);
    }

    public function update(PatientPackageRequest $request, string $id, Patient $patient): JsonResponse
    {
        $package = $this->patientPackageService->update($id, $request->validated(), $patient->id);
        
        return response()->json([
            'message' => 'Patient package updated successfully',
            'data' => new PatientPackageResource($package)
        ]);
    }

    public function destroy(string $id, Patient $patient): JsonResponse
    {
        $this->patientPackageService->delete($id, $patient->id);
        
        return response()->json([
            'message' => 'Patient package deleted successfully'
        ]);
    }

    public function getActivePackages(Request $request, Patient $patient): AnonymousResourceCollection
    {
        $packages = $this->patientPackageService->getActivePackages($patient->id);
        
        return PatientPackageResource::collection($packages);
    }
}
