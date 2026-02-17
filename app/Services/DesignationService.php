<?php

namespace App\Services;

use App\Models\Designation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use App\Helpers\EntityDataHelper;

class DesignationService
{
    public function getAllDesignations(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Designation::query();

        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
        }

        return $query->latest('CreatedOn')->paginate($perPage);
    }

    public function getDesignationById(string $id): ?Designation
    {
        return Designation::find($id);
    }

    public function createDesignation(array $data): Designation
    {
          $data1= EntityDataHelper::prepareForCreation($data);
        return Designation::create($data1);
       
    }

    public function updateDesignation(string $id, array $data): Designation
    {
          $data1= EntityDataHelper::prepareForUpdate($data);
        return DB::transaction(function () use ($id, $data1) {
            $designation = $this->getDesignationById($id);
            
            if (!$designation) {
                throw new ModelNotFoundException("Designation not found");
            }
            
            $designation->update($data1);
            return $designation->fresh();
        });
    }

    public function deleteDesignation(string $id): bool
    {
        return DB::transaction(function () use ($id) {
            $designation = $this->getDesignationById($id);
            
            if (!$designation) {
                throw new ModelNotFoundException("Designation not found");
            }
            
            return $designation->delete();
        });
    }
}
