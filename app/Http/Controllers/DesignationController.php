<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\DesignationRequest;
use App\Http\Resources\DesignationResource;
use App\Services\DesignationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DesignationController extends Controller
{
    protected DesignationService $designationService;

    public function __construct(DesignationService $designationService)
    {
        $this->designationService = $designationService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = $request->input('per_page', 10);
        $filters = [
            'search' => $request->input('search'),
            'is_active' => $request->input('is_active')
        ];

        $designations = $this->designationService->getAllDesignations(
            array_filter($filters),
            $perPage
        );

        return DesignationResource::collection($designations)
            ->additional([
                'success' => true,
                'message' => 'Designations retrieved successfully.',
            ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DesignationRequest $request): JsonResponse
    {
        try {

            $designation = $this->designationService->createDesignation($request->validated());
            
            return (new DesignationResource($designation))
                ->additional([
                    'success' => true,
                    'message' => 'Designation created successfully.'
                ])
                ->response()
                ->setStatusCode(201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create designation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $designation = $this->designationService->getDesignationById($id);
            
            if (!$designation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Designation not found',
                ], 404);
            }
            
            return (new DesignationResource($designation))
                ->additional([
                    'success' => true,
                    'message' => 'Designation retrieved successfully.'
                ])
                ->response();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve designation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DesignationRequest $request, string $id): JsonResponse
    {
        try {
            $designation = $this->designationService->updateDesignation($id, $request->validated());
            
            return (new DesignationResource($designation))
                ->additional([
                    'success' => true,
                    'message' => 'Designation updated successfully.'
                ])
                ->response();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Designation not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update designation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->designationService->deleteDesignation($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Designation deleted successfully.'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Designation not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete designation',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
