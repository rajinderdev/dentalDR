<?php

namespace App\Http\Controllers;

use App\Http\Requests\DigitalSignatureRequest;
use App\Http\Resources\DigitalSignatureResource;
use App\Services\DigitalSignatureService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Class DigitalSignatureController
 * 
 * @package App\Http\Controllers
 */
class DigitalSignatureController extends Controller
{
    protected $digitalSignatureService;
    
    public function __construct(DigitalSignatureService $digitalSignatureService)
    {
        $this->digitalSignatureService = $digitalSignatureService;
    }
    
    /**
     * Save digital signature
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(DigitalSignatureRequest $request): JsonResponse
    {
        try {
            $signature = $this->digitalSignatureService->saveSignature($request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Digital signature saved successfully',
                'data' => new DigitalSignatureResource($signature)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save digital signature: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get signatures by patient
     *
     * @param string $patientId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByPatient($patientId)
    {
        try {
            $signatures = $this->digitalSignatureService->getSignaturesByPatient($patientId);
           
            return response()->json([
                'success' => true,
                'data' => DigitalSignatureResource::collection($signatures)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve signatures: ' . $e->getMessage()
            ], 500);
        }
    }
}
