<?php

namespace App\Services;

use App\Models\ProviderSlot; // Assuming you have a ProviderSlot model
use App\Http\Resources\ProviderSlotResource; // Assuming you have a resource for Provider Slot
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

class ProviderSlotService
{
    /**
     * Get a paginated list of Provider Slots.
     *
     * @param int $perPage
     * @param array $filters
     * @return array
     */
    public function getProviderSlots(int $perPage, array $filters = []): array
    {
        $query = ProviderSlot::query();

        // Filter by providerId if provided
        if (!empty($filters['providerId'])) {
            $query->where('ProviderID', $filters['providerId']);
        }

        // Filter by date if provided
        if (!empty($filters['date'])) {
            $query->whereDate('StartDatetime', $filters['date']);
        }
        if(!empty($filters['providerId']) || !empty($filters['date'])){
            $data = $query->get();

            return [
                'provider_slots' => $data,
                'pagination' => [
                    'current_page' => 1,
                    'per_page' => $data->count(),
                    'total' => $data->count(),
                    
                ]
            ];
        }
        else{
            $data = $query->paginate($perPage);

            return [
                'provider_slots' => $data, // Transform the data using the resource
                'pagination' => [
                    'current_page' => $data->currentPage(),
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                    
                ]
            ];
        }
        
    }

    /**
     * Create a new provider slot record.
     *
     * @param array $data The validated data for creating the provider slot
     * @return ProviderSlot The newly created provider slot model
     */
    public function createProviderSlot(array $data): ProviderSlot
    {
        return ProviderSlot::create($data);
    }

    /**
     * Update an existing provider slot record.
     *
     * @param ProviderSlot $providerSlot The provider slot model to update
     * @param array $data The validated data for updating the provider slot
     * @return ProviderSlot The updated provider slot model
     */
    public function updateProviderSlot(ProviderSlot $providerSlot, array $data): ProviderSlot
    {
        $providerSlot->update($data);
        return $providerSlot;
    }
}