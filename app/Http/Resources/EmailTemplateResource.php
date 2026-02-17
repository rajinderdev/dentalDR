<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmailTemplateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'ClinicID' => $this->ClinicID,
            'SituationID' => $this->SituationID,
            'EmailCategoryID' => $this->EmailCategoryID,
            'FromEmailID' => $this->FromEmailID,
            'BCCEmailID' => $this->BCCEmailID,
            'SubjectEnglish' => $this->SubjectEnglish,
            'BodyEnglish' => $this->BodyEnglish,
            'EffectiveDate' => $this->EffectiveDate,
            'IsDeleted' => $this->IsDeleted,
            'CreatedOn' => $this->CreatedOn,
            'CreatedBy' => $this->CreatedBy,
            'LastUpdatedOn' => $this->LastUpdatedOn,
            'LastUpdatedBy' => $this->LastUpdatedBy
        ];
    }
}