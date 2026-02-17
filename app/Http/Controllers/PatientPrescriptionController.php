<?php

namespace App\Http\Controllers;

use App\Helpers\EntityDataHelper;
use App\Models\PatientPrescription;
use App\Http\Resources\PatientPrescriptionResource; // Assuming you have a resource for Patient Prescription
use App\Http\Resources\PatientInvestigationResource;
use App\Services\PatientPrescriptionService; // Assuming you have a service for handling business logic
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Traits\ApiResponse;
use App\Http\Requests\StorePatientPrescriptionRequest; // Assuming you have a request for storing prescriptions
use App\Http\Requests\UpdatePatientPrescriptionRequest; // Assuming you have a request for updating prescriptions
use App\Models\Patient;

/**
 * @group Patient
 * @subgroup Prescriptions
 * @subgroupDescription PatientPrescriptionController handles the CRUD operations for patient prescriptions controller.
 */
class PatientPrescriptionController extends Controller
{
    use ApiResponse; // Use the ApiResponse trait for consistent API responses

    public function __construct(private PatientPrescriptionService $patientPrescriptionService)
    {
    }

    /**
     * @group PatientPrescription
     *
     * @method GET
     *
     * List all patientprescription
     *
     * @get /
     *
     * @response 200 scenario="Success" {
     *     {
     *         "data": {
     *             "patient_prescriptions": [
     *                 {
     *                     "patient_prescription_id": 1,
     *                     "patient_id": 1,
     *                     "provider_id": 1,
     *                     "prescription_note": "Example value",
     *                     "date_of_prescription": "Example value",
     *                     "next_follow_up": "Example value",
     *                     "investigation_advised_ids": 1,
     *                     "patient_investigation_id": 1,
     *                     "is_deleted": true,
     *                     "created_on": "Example value",
     *                     "created_by": "Example value",
     *                     "last_updated_on": "Example value",
     *                     "last_updated_by": "Example value",
     *                     "rowguid": 1,
     *                     "is_followup_sms_required": true
     *                 }
     *             ],
     *             "pagination": {
     *                 "current_page": 1,
     *                 "per_pages": 50,
     *                 "total": 100
     *             }
     *         }
     *     }
     * }
     *
     * @response 500 {"message": "Internal server error"}
     *
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function index(Request $request, $patient=null)
    {
        try {
            // Handle undefined or invalid patient ID
            if (!$patient || $patient === 'undefined') {
                return $this->successResponse([
                    'prescriptions' => [],
                    'pagination' => [
                        'current_page' => 1,
                        'per_page' => 50,
                        'total' => 0
                    ],
                    'message' => 'No valid patient specified'
                ]);
            }

            $perPage = $request->query('per_page', env('DEFAULT_PER_PAGE', 50));
            $data = $this->patientPrescriptionService->getPatientPrescriptions($patient, $perPage);

            // Transform data to match the clean structure format
            $prescriptions = $data['patient_prescriptions']->map(function ($prescription) {
                $prescriptionResource = new PatientPrescriptionResource($prescription);
                $prescriptionData = $prescriptionResource->toArray(request());
                return $prescriptionData;
            });

            return $this->successResponse([
                'prescriptions' => $prescriptions,
                'pagination' => [
                    'current_page' => $data['pagination']['current_page'],
                    'per_page' => $data['pagination']['per_page'],
                    'total' => $data['pagination']['total']
                ],
                'message' => 'Ok'
            ]);
        } catch (Exception $e) {
            Log::error('Error fetching Patient Prescriptions: ' . $e->getMessage());

            // Return empty array instead of error for better UX
            return $this->successResponse([
                'prescriptions' => [],
                'pagination' => [
                    'current_page' => 1,
                    'per_page' => 50,
                    'total' => 0
                ],
                'message' => 'Failed to fetch prescriptions - returning empty result'
            ]);
        }
    }

    /**
     * @group PatientPrescription
     *
     * @method POST
     *
     * Create a new patientprescription
     *
     * @post /
     *
     * @bodyParam PatientID string required. Maximum length: 255. Example: "Example PatientID"
     * @bodyParam ProviderID string required. Maximum length: 255. Example: "1"
     * @bodyParam PrescriptionNote string required. Maximum length: 255. Example: "Example PrescriptionNote"
     * @bodyParam DateOfPrescription string required. Maximum length: 255. Example: "Example DateOfPrescription"
     * @bodyParam NextFollowUp string required. Maximum length: 255. Example: "Example NextFollowUp"
     * @bodyParam InvestigationAdvisedIDCSV string optional. nullable. Maximum length: 255. Example: "Example InvestigationAdvisedIDCSV"
     * @bodyParam PatientInvestigationID string optional. nullable. Maximum length: 255. Example: "Example PatientInvestigationID"
     * @bodyParam IsDeleted string optional. nullable. Example: "Example IsDeleted"
     *
     * @response 201 scenario="Success" {
     *     {
     *         "data": {
     *             "prescription": {
     *                 "patient_prescription_id": 1,
     *                 "patient_id": 1,
     *                 "provider_id": 1,
     *                 "prescription_note": "Example value",
     *                 "date_of_prescription": "Example value",
     *                 "next_follow_up": "Example value",
     *                 "investigation_advised_ids": 1,
     *                 "patient_investigation_id": 1,
     *                 "is_deleted": true,
     *                 "created_on": "Example value",
     *                 "created_by": "Example value",
     *                 "last_updated_on": "Example value",
     *                 "last_updated_by": "Example value",
     *                 "rowguid": 1,
     *                 "is_followup_sms_required": true
     *             }
     *         }
     *     }
     * }
     *
     * @response 400 {"message": "Validation error", "errors": {"field": ["Error message"]}}
     * @response 500 {"message": "Internal server error"}
     *
     * @return PatientPrescriptionResource
     */
    public function store(StorePatientPrescriptionRequest $request)
    {
        try {
            $validatedData = $request->validated();
            
            // Validate Patient ID
            if (!$validatedData['PatientID'] || $validatedData['PatientID'] === 'undefined') {
                return $this->errorResponse([
                    'message' => 'Invalid patient ID provided',
                    'error' => 'Patient ID is required and cannot be undefined'
                ], 400);
            }
            
            $prescription = $this->patientPrescriptionService->createPrescription($validatedData);

            // Build response data similar to index API
            $prescriptionResource = new PatientPrescriptionResource($prescription);
            $responseData = $prescriptionResource->toArray(request());
            return $this->successResponse([
                'message' => 'Prescription created successfully',
                'prescription' => $responseData
            ], 201);
        } catch (Exception $e) {
            Log::error('Error creating prescription: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return $this->errorResponse([
                'message' => 'Failed to create prescription',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @group PatientPrescription
     *
     * @method PUT
     *
     * Update an existing patientprescription
     *
     * @put /{id}
     *
     * @urlParam id integer required. The ID of the patientprescription to update. Example: 1
     *
     * @bodyParam PatientID string required. Maximum length: 255. Example: "Example PatientID"
     * @bodyParam ProviderID string required. Maximum length: 255. Example: "1"
     * @bodyParam PrescriptionNote string required. Maximum length: 255. Example: "Example PrescriptionNote"
     * @bodyParam DateOfPrescription string required. Maximum length: 255. Example: "Example DateOfPrescription"
     * @bodyParam NextFollowUp string required. Maximum length: 255. Example: "Example NextFollowUp"
     * @bodyParam InvestigationAdvisedIDCSV string optional. nullable. Maximum length: 255. Example: "Example InvestigationAdvisedIDCSV"
     * @bodyParam PatientInvestigationID string optional. nullable. Maximum length: 255. Example: "Example PatientInvestigationID"
     * @bodyParam IsDeleted string optional. nullable. Example: "Example IsDeleted"
     *
     * @response 200 scenario="Success" {
     *     {
     *         "data": {
     *             "prescription": {
     *                 "patient_prescription_id": 1,
     *                 "patient_id": 1,
     *                 "provider_id": 1,
     *                 "prescription_note": "Example value",
     *                 "date_of_prescription": "Example value",
     *                 "next_follow_up": "Example value",
     *                 "investigation_advised_ids": 1,
     *                 "patient_investigation_id": 1,
     *                 "is_deleted": true,
     *                 "created_on": "Example value",
     *                 "created_by": "Example value",
     *                 "last_updated_on": "Example value",
     *                 "last_updated_by": "Example value",
     *                 "rowguid": 1,
     *                 "is_followup_sms_required": true
     *             }
     *         }
     *     }
     * }
     *
     * @response 400 {"message": "Validation error", "errors": {"field": ["Error message"]}}
     * @response 404 {"message": "Resource not found"}
     * @response 500 {"message": "Internal server error"}
     *
     * @return PatientPrescriptionResource
     */
    public function update(UpdatePatientPrescriptionRequest $request, PatientPrescription $patientPrescription)
    {
        try {
            $validatedData = $request->validated();
            $validatedData = EntityDataHelper::prepareForUpdate($validatedData);
            $updatedPrescription = $this->patientPrescriptionService->updatePrescription($patientPrescription, $validatedData);

            return $this->successResponse([
                'message' => 'Prescription updated successfully',
                'prescription' => new PatientPrescriptionResource($updatedPrescription)
            ]);
        } catch (Exception $e) {
            Log::error('Error updating prescription: ' . $e->getMessage());

            return $this->errorResponse([
                'message' => 'Failed to update prescription',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    

    /**
     * @group Patient
     *
     * @method GET
     *
     * Get a specific patient
     *
     * @get /{id}
     *
     * @urlParam id integer required. The ID of the patient to retrieve. Example: 1
     *
     * @response 500 {"message": "Internal server error"}
     *
     * @return \Illuminate\Http\Response
     */
    public function show($patientId, $id)
    {
        try {
            // Find the prescription or fail with 404
            $patientPrescription = $this->patientPrescriptionService->getPatientPrescriptionById($patientId, $id);
            // Convert single model to resource
            $prescriptionResource = new PatientPrescriptionResource($patientPrescription);
            $prescriptionData = $prescriptionResource->toArray(request());

            return $this->successResponse([
                'message' => 'Prescription retrieved successfully',
                'prescription' => $prescriptionData
            ], 200);
      
        } catch (Exception $e) {
            return $this->errorResponse([
                'message' => 'An error occurred while retrieving the prescription.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

     /**
     * @group Patient
     *
     * @method DELETE
     *
     * Delete a prescription
     *
     * @delete /{id}
     *
     * @urlParam id integer required. The ID of the prescription to delete. Example: 1
     *
     * @response 400 {"message": "Validation error", "errors": {"field": ["Error message"]}}
     * @response 404 {"message": "Resource not found"}
     * @response 500 {"message": "Internal server error"}
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($patientId, $id)
    {
        $this->patientPrescriptionService->deletePatientPrescription($id);
        
        return $this->successResponse([
            'message' => 'Prescription deleted successfully'
        ]);
    }

}
