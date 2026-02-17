<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTreatmentLabWorkRequest;
use App\Http\Resources\TreatmentLabWorkResource;
use App\Models\TreatmentLabWork;
use App\Models\Patient;
use App\Services\TreatmentLabWorkService;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TreatmentLabWorkController extends Controller
{
    use ApiResponse;

    public function __construct(private TreatmentLabWorkService $treatmentLabWorkService)
    {
    }

    public function index(Request $request,Patient $patient, string $patientTreatmentsDoneId)
    {
        try {
            $perPage = $request->query('per_page', env('DEFAULT_PER_PAGE', 50));
            $paginator = $this->treatmentLabWorkService->getByTreatmentDoneId($patient, $patientTreatmentsDoneId, (int) $perPage);
            return $this->successResponse([
                'treatment_lab_work' => TreatmentLabWorkResource::collection($paginator),
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                    'last_page' => $paginator->lastPage(),
                ]
            ]);
        } catch (Exception $e) {
            Log::error('Error fetching treatment lab work: ' . $e->getMessage());
            return $this->errorResponse([
                'message' => 'An error occurred while retrieving data. Please try again later.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function store(StoreTreatmentLabWorkRequest $request)
    {
        try {
            $data = $request->validated();
            $item = $this->treatmentLabWorkService->create($data);
            return $this->successResponse([
                'message' => 'Treatment lab work created successfully',
                'treatment_lab_work' => new TreatmentLabWorkResource($item),
            ], 201);
        } catch (Exception $e) {
            Log::error('Error creating treatment lab work: ' . $e->getMessage());
            return $this->errorResponse([
                'message' => 'Failed to create treatment lab work',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}


