<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\BuildingRequest;
use App\Services\BuildingService;
use Illuminate\Http\JsonResponse;
use App\Models\Building;
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    /**
     * @var BuildingService
     */
    protected $buildingService;

    /**
     * BuildingController constructor.
     *
     * @param BuildingService $buildingService
     */
    public function __construct(BuildingService $buildingService)
    {
        $this->buildingService = $buildingService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return $this->buildingService->getAllBuildings();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\BuildingRequest  $request
     * @return \App\Http\Resources\BuildingResource
     */
    public function store(BuildingRequest $request)
    {
        return $this->buildingService->createBuilding($request->validated());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \App\Http\Resources\BuildingResource
     */
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \App\Http\Resources\BuildingResource
     */
    public function show($id)
    {
        return $this->buildingService->getBuildingById($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\BuildingRequest  $request
     * @param  \App\Models\Building  $building
     * @return \App\Http\Resources\BuildingResource
     */
    public function update(BuildingRequest $request, Building $building)
    {
        return $this->buildingService->updateBuilding($building, $request->validated());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Building  $building
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Building $building)
    {
        $result = $this->buildingService->deleteBuilding($building);
        
        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message']
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => $result['message'],
            'error' => $result['error'] ?? null
        ], 500);
    }

    /**
     * Search buildings by name or code
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\JsonResponse
     */
    /**
     * Search buildings by name or code
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\JsonResponse
     */
    public function searchBuilding(Request $request)
    {
        $results = $this->buildingService->searchBuilding($request);
        
        if ($results->count() === 0) {
            return response()->json([
                'success' => false,
                'message' => 'No buildings found matching your criteria.',
                'data' => []
            ], 404);
        }
        
        return $results;
    }

    
}
