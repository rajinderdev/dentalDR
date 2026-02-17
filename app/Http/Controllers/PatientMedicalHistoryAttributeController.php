<?php

namespace App\Http\Controllers;

use App\Helpers\EntityDataHelper;
use App\Models\PatientMedicalHistoryAttribute;
use App\Http\Resources\PatientMedicalHistoryAttributeResource; // Assuming you have a resource for Patient Medical History Attribute
use App\Services\PatientMedicalHistoryAttributeService; // Assuming you have a service for handling business logic
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Traits\ApiResponse;
use App\Http\Requests\StorePatientMedicalHistoryAttributeRequest; // Assuming you have a request for storing medical history attributes
use App\Http\Requests\UpdatePatientMedicalHistoryAttributeRequest; // Assuming you have a request for updating medical history attributes
use App\Models\Patient;

/**
 * @group Patient
 * @subgroup MedicalHistoryAttribute
 * @subgroupDescription PatientMedicalHistoryAttributeController handles the CRUD operations for patient medical history attribute controller.
 */
class PatientMedicalHistoryAttributeController extends Controller
{
    use ApiResponse; // Use the ApiResponse trait for consistent API responses

    public function __construct(private PatientMedicalHistoryAttributeService $patientMedicalHistoryAttributeService)
    {
    }

    /**
     * @group PatientMedicalHistoryAttribute
     *
     * @method GET
     *
     * List all patientmedicalhistoryattribute
     *
     * @get /
     *
     * @response 200 scenario="Success" {
     *     {
     *         "data": {
     *             "MedicalHistoryAttributes": [
     *                 {
     *                     "PatientMedicalDetailId": 1,
     *                     "PatientId": 1,
     *                     "MedicalAttributesCategory": "Example value",
     *                     "MedicalAttributesId": 1,
     *                     "MedicalAttributeValue": "Example value",
     *                     "MedicalAttributeText": "Example value",
     *                     "MedicalHistoryDate": "Example value",
     *                     "LastUpdatedBy": "Example value",
     *                     "LastUpdatedOn": "Example value"
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
            if (!$patient || $patient === 'undefined') {
                return $this->successResponse([
                    'MedicalHistoryAttributes' => [],
                    'pagination' => [
                        'current_page' => 1,
                        'per_page' => 50,
                        'total' => 0,
                        'last_page' => 1
                    ],
                    'message' => 'Failed to fetch Medical History Attributes - returning empty result'
                ]);
            }
            $perPage = $request->query('per_page', env('DEFAULT_PER_PAGE', 50));
            $data = $this->patientMedicalHistoryAttributeService->getMedicalHistoryAttributes($patient, $perPage);

            return $this->successResponse([
                'MedicalHistoryAttributes' => PatientMedicalHistoryAttributeResource::collection($data['MedicalHistoryAttributes']),
                'pagination' => $data['pagination']
            ]);
        } catch (Exception $e) {
            Log::error('Error fetching Patient Medical History Attributes: ' . $e->getMessage());

            return $this->errorResponse([
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @group PatientMedicalHistoryAttribute
     *
     * @method POST
     *
     * Create a new patientmedicalhistoryattribute
     *
     * @post /
     *
     * @bodyParam PatientID string required. Maximum length: 255. Example: "Example PatientID"
     * @bodyParam MedicalAttributesCategory string required. Example: "Example MedicalAttributesCategory"
     * @bodyParam MedicalAttributesID string required. Maximum length: 255. Example: "Example MedicalAttributesID"
     * @bodyParam MedicalAttributeValue string required. Example: "Example MedicalAttributeValue"
     * @bodyParam MedicalAttributeText string required. Example: "Example MedicalAttributeText"
     * @bodyParam MedicalHistoryDate string required. date. Example: "Example MedicalHistoryDate"
     * @bodyParam LastUpdatedBy string required. date. Example: "Example LastUpdatedBy"
     * @bodyParam LastUpdatedOn string required. date. Example: "Example LastUpdatedOn"
     *
     * @response 201 scenario="Success" {
     *     {
     *         "data": {
     *             "MedicalHistoryAttribute": {
     *                 "PatientMedicalDetailId": 1,
     *                 "PatientId": 1,
     *                 "MedicalAttributesCategory": "Example value",
     *                 "MedicalAttributesId": 1,
     *                 "MedicalAttributeValue": "Example value",
     *                 "MedicalAttributeText": "Example value",
     *                 "MedicalHistoryDate": "Example value",
     *                 "LastUpdatedBy": "Example value",
     *                 "LastUpdatedOn": "Example value"
     *             }
     *         }
     *     }
     * }
     *
     * @response 400 {"message": "Validation error", "errors": {"field": ["Error message"]}}
     * @response 500 {"message": "Internal server error"}
     *
     * @return PatientMedicalHistoryAttributeResource
     */
    public function store(StorePatientMedicalHistoryAttributeRequest $request, Patient $patient)
    {
        try {
            $validatedData = $request->validated();
            $validatedData['PatientID']=$patient->PatientID;
            $validatedData = EntityDataHelper::prepareForCreation($validatedData);
            $medicalHistory = $this->patientMedicalHistoryAttributeService->createMedicalHistoryAttribute($validatedData);

            return $this->successResponse([
                'message' => 'Medical history attribute created successfully',
                'medical_history_attribute' => new PatientMedicalHistoryAttributeResource($medicalHistory)
            ], 201);
        } catch (Exception $e) {
            Log::error('Error creating medical history attribute: ' . $e->getMessage());

            return $this->errorResponse([
                'message' => 'Failed to create medical history attribute',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @group PatientMedicalHistoryAttribute
     *
     * @method PUT
     *
     * Update an existing patientmedicalhistoryattribute
     *
     * @put /{id}
     *
     * @urlParam id integer required. The ID of the patientmedicalhistoryattribute to update. Example: 1
     *
     * @bodyParam PatientID string optional. Maximum length: 255. Example: "Example PatientID"
     * @bodyParam MedicalAttributesCategory string optional. Example: "Example MedicalAttributesCategory"
     * @bodyParam MedicalAttributesID string optional. Maximum length: 255. Example: "Example MedicalAttributesID"
     * @bodyParam MedicalAttributeValue string optional. Example: "Example MedicalAttributeValue"
     * @bodyParam MedicalAttributeText string optional. Example: "Example MedicalAttributeText"
     * @bodyParam MedicalHistoryDate string optional. date. Example: "Example MedicalHistoryDate"
     * @bodyParam LastUpdatedBy string optional. date. Example: "Example LastUpdatedBy"
     * @bodyParam LastUpdatedOn string optional. date. Example: "Example LastUpdatedOn"
     *
     * @response 200 scenario="Success" {
     *     {
     *         "data": {
     *             "MedicalHistoryAttribute": {
     *                 "PatientMedicalDetailId": 1,
     *                 "PatientId": 1,
     *                 "MedicalAttributesCategory": "Example value",
     *                 "MedicalAttributesId": 1,
     *                 "MedicalAttributeValue": "Example value",
     *                 "MedicalAttributeText": "Example value",
     *                 "MedicalHistoryDate": "Example value",
     *                 "LastUpdatedBy": "Example value",
     *                 "LastUpdatedOn": "Example value"
     *             }
     *         }
     *     }
     * }
     *
     * @response 400 {"message": "Validation error", "errors": {"field": ["Error message"]}}
     * @response 404 {"message": "Resource not found"}
     * @response 500 {"message": "Internal server error"}
     *
     * @return PatientMedicalHistoryAttributeResource
     */
    public function update(UpdatePatientMedicalHistoryAttributeRequest $request, PatientMedicalHistoryAttribute $patientMedicalHistoryAttribute)
    {
        try {
            $validatedData = $request->validated();
            $validatedData = EntityDataHelper::prepareForUpdate($validatedData);
            $updatedMedicalHistory = $this->patientMedicalHistoryAttributeService->updateMedicalHistoryAttribute($patientMedicalHistoryAttribute, $validatedData);

            return $this->successResponse([
                'message' => 'Medical history attribute updated successfully',
                'medical_history_attribute' => new PatientMedicalHistoryAttributeResource($updatedMedicalHistory)
            ]);
        } catch (Exception $e) {
            Log::error('Error updating medical history attribute: ' . $e->getMessage());

            return $this->errorResponse([
                'message' => 'Failed to update medical history attribute',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
