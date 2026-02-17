<?php

namespace App\Services;

use App\Models\SMSSituation;
use App\Http\Resources\SMSSituationResource;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Helpers\EntityDataHelper;

class SMSSituationService
{
    /**
     * Get a paginated list of SMS Situations.
     *
     * @param int $perPage
     * @return array
     */
    public function getSMSSituations(int $perPage): array
    {
        $data = SMSSituation::where('isDeleted', false)->orderBy('CreatedOn','desc')->paginate($perPage);

        return [
            'sms_situations' => $data,
            'pagination' => [
                'current_page' => $data->currentPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ]
        ];
    }

    /**
     * Create a new SMS situation record.
     *
     * @param array $data The validated data for creating the SMS situation
     * @return SMSSituation The newly created SMS situation model
     */
    public function createSMSSituation(array $data): SMSSituation
    {
        $validatedData = EntityDataHelper::prepareForCreation($data); 
        return SMSSituation::create($validatedData);
    }

    /**
     * Update an existing SMS situation record.
     *
     * @param SMSSituation $sMSSituation The SMS situation model to update
     * @param array $data The validated data for updating the SMS situation
     * @return SMSSituation The updated SMS situation model
     */
    public function updateSMSSituation(SMSSituation $sMSSituation, array $data): SMSSituation
    {
        $validatedData = EntityDataHelper::prepareForUpdate($data);
        $sMSSituation->update($validatedData);
        return $sMSSituation;
    }

     /**
     * Delete a SMS situation record.
     *
     * @param SMSSituation $sMSSituation The SMS situation model to delete
     * @return SMSSituation The updated SMS situation model
     */
    public function deleteSMSSituation(SMSSituation $sMSSituation): SMSSituation
    {
        $sMSSituation->update(['isDeleted' => true]);
        return $sMSSituation;
    }
}

