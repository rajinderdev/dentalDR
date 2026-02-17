<?php

namespace App\Services;

use App\Http\Resources\BuildingResource;
use App\Models\Building;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BuildingService
{
    /**
     * Get all non-deleted buildings
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getAllBuildings(): AnonymousResourceCollection
    {
        $buildings = Building::notDeleted()
            ->orderBy('CreatedOn', 'desc')
            ->get();
            
        return BuildingResource::collection($buildings);
    }

    /**
     * Get a specific building by ID
     *
     * @param int $id
     * @return \App\Http\Resources\BuildingResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getBuildingById($id): BuildingResource
    {
        $building = Building::notDeleted()->findOrFail($id);
        return new BuildingResource($building);
    }

    /**
     * Create a new building
     *
     * @param array $data
     * @return \App\Http\Resources\BuildingResource
     */
    public function createBuilding(array $data): BuildingResource
    {
        $building = Building::create($data);
        return new BuildingResource($building);
    }

    /**
     * Update an existing building
     *
     * @param \App\Models\Building $building
     * @param array $data
     * @return \App\Http\Resources\BuildingResource
     */
    public function updateBuilding(Building $building, array $data): BuildingResource
    {
        $building->update($data);
        return new BuildingResource($building->refresh());
    }

    /**
     * Soft delete a building
     *
     * @param \App\Models\Building $building
     * @return array
     */
    public function deleteBuilding(Building $building): array
    {
        try {
            DB::beginTransaction();
            $building->delete();
            DB::commit();
            
            return [
                'success' => true,
                'message' => 'Building deleted successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete building: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to delete building.',
                'error' => $e->getMessage()
            ];
        }
    }
    /**
     * Search buildings by name or code
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function searchBuilding($request)
    {
        $searchTerm = $request->input('search', '');
        
        $buildings = Building::notDeleted()
            ->where(function($query) use ($searchTerm) {
                $query->where('building_name', $searchTerm);
            })
            ->orderBy('CreatedOn', 'desc')
            ->get();
            
        return BuildingResource::collection($buildings);
    }
}
