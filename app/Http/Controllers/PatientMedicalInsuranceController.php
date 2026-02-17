<?php

namespace App\Http\Controllers;

use App\Http\Resources\PatientMedicalInsuranceResource;
use App\Models\Patient;
use App\Models\PatientMedicalInsurance;
use App\Services\PatientMedicalInsuranceService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\PatientMedicalInsuranceRequest;
use Symfony\Component\HttpFoundation\Response;

class PatientMedicalInsuranceController extends Controller
{
    protected $insuranceService;

    public function __construct(PatientMedicalInsuranceService $insuranceService)
    {
        $this->insuranceService = $insuranceService;
    }


    /**
     * Get insurances by patient ID.
     */
    public function getByPatient(Patient $patient): JsonResponse
    {
        $insurances = $this->insuranceService->getInsurancesByPatient($patient->PatientID);
        return PatientMedicalInsuranceResource::collection($insurances)->response();
    }

    /**
     * Store a newly created patient medical insurance in storage.
     */
    public function store(PatientMedicalInsuranceRequest $request, Patient $patient): JsonResponse
    {
        $insurance = $this->insuranceService->createInsurance($request->validated(), $patient->PatientID);
        
        return (new PatientMedicalInsuranceResource($insurance))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }


    /**
     * Update the specified patient medical insurance in storage.
     */
   public function update(PatientMedicalInsuranceRequest $request, string $patientId, string $patient_medical_insurance): JsonResponse
    {
        $updatedInsurance = $this->insuranceService->updateInsurance(
            $patient_medical_insurance,
            $patientId,
            $request->validated()
        );
        
        return (new PatientMedicalInsuranceResource($updatedInsurance))->response();
    }

    public function destroy(string $patient_medical_insurance, string $patientId): JsonResponse
    {       
        $this->insuranceService->deleteInsurance(
            $patient_medical_insurance, 
            $patientId
        );
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

}
