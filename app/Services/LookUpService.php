<?php

namespace App\Services;

use App\Models\LookUp;
use Illuminate\Database\Eloquent\Builder;
use App\Helpers\EntityDataHelper;
class LookUpService
{
    /**
     * Get a paginated list of LookUps.
     *
     * @param int $perPage
     * @return array
     */
    public function getLookUps(int $perPage, $category = null): array
    {
        $data = LookUp::when($category, function(Builder $query) use ($category) {
            $query->where('ItemCategory', $category);
        })->where('IsDeleted', 0)->orderBy('LastUpdatedOn', 'desc')->paginate($perPage);

        return [
            'lookups' => $data,
            'pagination' => [
                'current_page' => $data->currentPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ]
        ];
    }

    /**
     * Create a new lookup record.
     *
     * @param array $data The validated data for creating the lookup
     * @return LookUp The newly created lookup model
     */
    public function createLookup(array $data): LookUp
    {
        $validatedData = EntityDataHelper::prepareForCreation($data);
        return LookUp::create($validatedData);
    }

    /**
     * Update an existing lookup record.
     *
     * @param LookUp $lookup The lookup model to update
     * @param array $data The validated data for updating the lookup
     * @return LookUp The updated lookup model
     */
    public function updateLookup(LookUp $lookup, array $data): LookUp
    {
        $validatedData = EntityDataHelper::prepareForUpdate($data);
        $lookup->update($validatedData);
        return $lookup;
    }

    public function deleteLookUp(LookUp $lookUp): LookUp
    {
        $lookUp->update([
            'IsDeleted' => true,
        ]);
        return $lookUp;
    }
}