<?php

namespace App\Services;

use App\Models\Family;
use App\Http\Resources\FamilyResource;
use App\Helpers\EntityDataHelper;

class FamilyService
{
    /**
     * Get a paginated list of Families.
     *
     * @param int $perPage
     * @return array
     */
   
   
   
   public function getFamilies(int $perPage, ?string $search = null): array
    {
        $query = Family::orderBy('CreatedOn', 'desc');

        $search = $search !== null ? trim($search) : null;
        if ($search !== null && $search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('FamilyID', $search)
                    ->orWhere('FamilyName', 'like', '%' . $search . '%');
            });
        }

        $data = $query->paginate($perPage)->appends(['search' => $search]);

        return [
            'families' => $data,
            'pagination' => [
                'current_page' => $data->currentPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ]
        ];
    }

    public function createFamily(array $data): Family
    {
        $data = EntityDataHelper::prepareForCreation($data);
        return Family::create($data);
    }

    public function updateFamily(Family $family, array $data): Family
    {
        $data = EntityDataHelper::prepareForUpdate($data);
        $family->update($data);
        $family->fresh();
        
        return $family;
    }
}