<?php

namespace App\Services;

use App\Models\SMSTemplatesTag;
use App\Http\Resources\SMSTemplateTagResource;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Helpers\EntityDataHelper;

class SMSTemplatesTagService
{
    /**
     * Get a paginated list of SMS Templates Tags.
     *
     * @param int $perPage
     * @return array
     */
    public function getSMSTemplatesTags(int $perPage): array
    {
        $data = SMSTemplatesTag::where('isDeleted', false)->orderBy('SMSTemplatesTagID','desc')->paginate($perPage);

        return [
            'sms_templates_tags' => $data,
            'pagination' => [
                'current_page' => $data->currentPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ]
        ];
    }

    /**
     * Create a new SMS templates tag record.
     *
     * @param array $data The validated data for creating the SMS templates tag
     * @return SMSTemplatesTag The newly created SMS templates tag model
     */
    public function createSMSTemplatesTag(array $data): SMSTemplatesTag
    {
        $validatedData = EntityDataHelper::prepareForCreation($data);
        return SMSTemplatesTag::create($validatedData);
    }

    /**
     * Update an existing SMS templates tag record.
     *
     * @param SMSTemplatesTag $sMSTemplatesTag The SMS templates tag model to update
     * @param array $data The validated data for updating the SMS templates tag
     * @return SMSTemplatesTag The updated SMS templates tag model
     */
    public function updateSMSTemplatesTag(SMSTemplatesTag $sMSTemplatesTag, array $data): SMSTemplatesTag
    {
        $validatedData = EntityDataHelper::prepareForUpdate($data);
        $sMSTemplatesTag->update($validatedData);
        return $sMSTemplatesTag;
    }
    
    /**
     * Delete a SMS templates tag record.
     *
     * @param SMSTemplatesTag $sMSTemplatesTag The SMS templates tag model to delete
     * @return SMSTemplatesTag The updated SMS templates tag model
     */
    public function deleteSMSTemplatesTag(SMSTemplatesTag $sMSTemplatesTag): SMSTemplatesTag
    {
        $sMSTemplatesTag->update(['IsDeleted' => true]);
        return $sMSTemplatesTag;
    }
}
