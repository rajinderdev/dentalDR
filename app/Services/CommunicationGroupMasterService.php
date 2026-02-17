<?php

namespace App\Services;

use App\Models\CommunicationGroupMaster;
use App\Http\Resources\CommunicationGroupMasterResource;

class CommunicationGroupMasterService
{
    /**
     * Get a paginated list of communication groups.
     *
     * @param int $perPage
     * @return array
     */
    public function getCommunicationGroups(int $perPage, ?string $search = null): array
{
    $data = CommunicationGroupMaster::when($search, function ($query) use ($search) {
        return $query->where('CommunicationGroupMasterGuid', $search);  // Exact match
    })->paginate($perPage)->appends(['search' => $search]);
    
    return [
        'communication_groups' => $data,
        'pagination' => [
            'current_page' => $data->currentPage(),
            'per_page' => $data->perPage(),
            'total' => $data->total(),
        ]
    ];
}


    /**
     * Create a new communication group master record.
     *
     * @param array $data The validated data for creating the communication group master
     * @return CommunicationGroupMaster The newly created communication group master model
     */
    public function createCommunicationGroupMaster(array $data): CommunicationGroupMaster
    {
        return CommunicationGroupMaster::create($data);
    }

    /**
     * Update an existing communication group master record.
     *
     * @param CommunicationGroupMaster $communicationGroupMaster The communication group master model to update
     * @param array $data The validated data for updating the communication group master
     * @return CommunicationGroupMaster The updated communication group master model
     */
    public function updateCommunicationGroupMaster(CommunicationGroupMaster $communicationGroupMaster, array $data): CommunicationGroupMaster
    {
        $communicationGroupMaster->update($data);
        return $communicationGroupMaster;
    }
}