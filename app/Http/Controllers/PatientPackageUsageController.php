<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientPackageUsageRequest;
use App\Http\Resources\PatientPackageUsageResource;
use App\Services\PatientPackageUsageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Models\PatientPackage;
use App\Models\Patient;
use App\Models\PatientPackageUsage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PatientPackageUsageController extends Controller
{
    protected $patientPackageUsageService;

    public function __construct(PatientPackageUsageService $patientPackageUsageService)
    {
        $this->patientPackageUsageService = $patientPackageUsageService;
    }

    /**
     * Get all package usages
     */
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Patient $patient): AnonymousResourceCollection
    {
        $perPage = $request->input('per_page', 10);
        $filters = $request->only(['patient_package_id', 'treatment_id', 'provider_id', 'date_from', 'date_to']);
        
        // If service is available, use it
        if (class_exists(PatientPackageUsageService::class)) {
            $usages = $this->patientPackageUsageService->getAll(
                $perPage, 
                array_merge($filters, ['patient_id' => $patient->PatientID])
            );
            return PatientPackageUsageResource::collection($usages);
        }
        
        // Fallback to direct query if service is not available
        $query = PatientPackageUsage::where('PatientID', $patient->PatientID)
            ->with(['patientPackage', 'treatment', 'provider'])
            ->latest();
            
        // Apply filters
        if (!empty($filters['patient_package_id'])) {
            $query->where('PatientPackageID', $filters['patient_package_id']);
        }
        
        if (!empty($filters['treatment_id'])) {
            $query->where('TreatmentID', $filters['treatment_id']);
        }
        
        if (!empty($filters['provider_id'])) {
            $query->where('ProviderID', $filters['provider_id']);
        }
        
        if (!empty($filters['date_from'])) {
            $query->whereDate('CreatedOn', '>=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $query->whereDate('CreatedOn', '<=', $filters['date_to']);
        }
        
        return PatientPackageUsageResource::collection($query->paginate($perPage));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PatientPackageUsageRequest $request, Patient $patient): JsonResponse
    {
        try {
            DB::beginTransaction();
            
            $data = $request->validated();
            $data['PatientID'] = $patient->PatientID;
            $data['CreatedBy'] = Auth::id();
            $data['CreatedOn'] = now();
            
            // If service is available, use it
            if (class_exists(PatientPackageUsageService::class)) {
                $usage = $this->patientPackageUsageService->create($data);
            } else {
                $usage = PatientPackageUsage::create($data);
            }
            
            DB::commit();
            
            return response()->json([
                'message' => 'Package usage recorded successfully',
                'data' => new PatientPackageUsageResource($usage->load(['patientPackage', 'treatment', 'provider']))
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to record package usage',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient, PatientPackageUsage $usage): JsonResponse
    {
        // Verify the usage belongs to the patient
        if ($usage->PatientID !== $patient->PatientID) {
            return response()->json([
                'message' => 'Package usage not found for this patient',
            ], 404);
        }
        
        return response()->json([
            'data' => new PatientPackageUsageResource($usage->load(['patientPackage', 'treatment', 'provider']))
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PatientPackageUsageRequest $request, Patient $patient, PatientPackageUsage $usage): JsonResponse
    {
        // Verify the usage belongs to the patient
        if ($usage->PatientID !== $patient->PatientID) {
            return response()->json([
                'message' => 'Package usage not found for this patient',
            ], 404);
        }
        
        try {
            DB::beginTransaction();
            
            $data = $request->validated();
            $data['LastUpdatedBy'] = Auth::id();
            $data['LastUpdatedOn'] = now();
            
            // If service is available, use it
            if (class_exists(PatientPackageUsageService::class)) {
                $usage = $this->patientPackageUsageService->update($usage, $data);
            } else {
                $usage->update($data);
            }
            
            DB::commit();
            
            return response()->json([
                'message' => 'Package usage updated successfully',
                'data' => new PatientPackageUsageResource($usage->load(['patientPackage', 'treatment', 'provider']))
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update package usage',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient, PatientPackageUsage $usage): JsonResponse
    {
        // Verify the usage belongs to the patient
        if ($usage->PatientID !== $patient->PatientID) {
            return response()->json([
                'message' => 'Package usage not found for this patient',
            ], 404);
        }
        
        try {
            DB::beginTransaction();
            
            // If service is available, use it
            if (class_exists(PatientPackageUsageService::class)) {
                $this->patientPackageUsageService->delete($usage);
            } else {
                $usage->delete();
            }
            
            DB::commit();
            
            return response()->json([
                'message' => 'Package usage deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete package usage',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}