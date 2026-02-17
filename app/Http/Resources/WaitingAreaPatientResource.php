<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WaitingAreaPatientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->WaitingAreaID,
            'PatientTreatmentDoneID'              => $this->PatientTreatmentDoneID,
            'patient_id'      => $this->PatientID,
            'start_date_time' => $this->StartDateTime,
            'CreatedOn'       => $this->CreatedOn ? \Carbon\Carbon::parse($this->CreatedOn)->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s') : null,
            'AppointmentID' =>  $this->AppointmentID,
            'arrival_time'    => $this->ArrivalTime ? \Carbon\Carbon::parse($this->ArrivalTime)->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s') : null,
            'end_date_time'   => $this->EndDateTime,
            'patient_name'    => $this->PatientName,
            'patient_email'   => $this->patient->EmailAddress1 ?? null,
            'patient_gender'  => $this->patient->Gender ?? null,
            'patient_phone'   => $this->patient->MobileNumber ?? null,
            'patient_image'   => $this->patient && $this->patient->ImagePath?asset($this->patient->ImagePath):NULL,
            'status'          => $this->Status,
            'token_number'    => $this->TokenNumber,
            'reason'          => $this->Comments,
            'apt_type'        => null,
            'provider' => [
                'provider_id'   => $this->provider->ProviderID ?? null,
                'provider_name' => $this->provider->ProviderName ?? null,
            ],
        ];
    }
}
