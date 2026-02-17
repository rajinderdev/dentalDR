<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\PatientInvoice;
use App\Models\PatientTreatmentsDone;

class PatientTreatmentsDoneResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $invoice = PatientInvoice::where('PatientTreatmentDoneID', $this->id)->first();
        $lastchildern = PatientTreatmentsDone::where('ParentPatientTreatmentDoneID', $this->id)->orderBy('AddedOn','DESC')->first();
        $result = [
            'ProviderID' => $this->ProviderID,
            'TreatmentCost' => $this->TreatmentCost,
            'TreatmentDiscount' => $this->TreatmentDiscount,
            'TreatmentAddition' => $this->TreatmentAddition,
            'TreatmentBalance' => $this->TreatmentBalance,
            'TeethTreatmentNote' => $this->TeethTreatmentNote,
            'TreatmentDate' => $this->TreatmentDate,
            'TreatmentTypeID' => $this->treatment_type?$this->treatment_type->TreatmentTypeID:"",
            'TeethTreatment' => $this->treatment_type?$this->treatment_type->TeethTreatment:[],
            'TreatmentTotalCost' => $this->TreatmentTotalCost,
            'TreatmentPayment' => $this->TreatmentPayment,
            'TreatmentSubTypeID' => $this->treatment_type?$this->treatment_type->TreatmentSubTypeID:"",
            'ParentPatientTreatmentDoneID' => $this->ParentPatientTreatmentDoneID,
            // Additional data maintained for compatibility
            'ID' => $this->id,
            'PatientID' => $this->PatientID,
            'PatientName'   => $this->patient->fullName ?? null,
            'PatientGender'   => $this->patient->Gender ?? null,
            'PatientMobile'   => $this->patient->MobileNumber ?? null,
            'PatientEmail'   => $this->patient->EmailAddress1 ?? null,
            'PatientImage'   => $this->patient && $this->patient->ImagePath ? asset($this->patient->ImagePath) : null,
            'ProviderName' => $this->doctor->ProviderName ?? null,
            'TreatmentTax' => $this->TreatmentTax,
            'AmountToBeCollected' => $this->AmountToBeCollected,
            'isPrimaryTooth' => $this->isPrimaryTooth,
            'Status' => $this->IsArchived == 1 ? 'completed' : 'ongoing',
            'WaitingAreaFlag' => $this->WaitingAreaFlag,
            'invoice' => $invoice,
            'final_bal'=> $lastchildern ? $lastchildern->TreatmentBalance : $this->TreatmentBalance,
            
        ];
        $treatmentTypes = $this->treatment_types;
        if($treatmentTypes && $treatmentTypes->isNotEmpty()) {
            // Build TreatmentSteps array
            $result['TreatmentSteps'] = [];
            foreach($treatmentTypes as $tType) {
                $result['TreatmentSteps'][] = [
                    'PatientTreatmentTypeDoneID' => $tType->PatientTreatmentTypeDoneID,
                    'TreatmentTypeID' => $tType->TreatmentTypeID,
                    'TreatmentSubTypeID' => $tType->TreatmentSubTypeID,
                    'TeethTreatmentNote' => $tType->TeethTreatmentNote,
                    'TreatmentAddition' => $tType->Addition ?? "",
                    'treatmentType' => $tType->treatmentTypeHierarchy,
                    'subTreatmentType' => $tType->subTreatmentTypeHierarchy,
                ];
            }
             
            // Include all treatment types as an array for backward compatibility
            $result['TreatmentType'] = [];
            $treatmentType = $this->treatment_type;
            if($treatmentType) {
                $result['TreatmentType'] = [
                    'ID' => $treatmentType->id,
                     'PatientTreatmentTypeDoneID' => $tType->PatientTreatmentTypeDoneID,
                    'TreatmentTypeID' => $treatmentType->TreatmentTypeID,
                    'TreatmentSubTypeID' => $treatmentType->TreatmentSubTypeID,
                    'TreatmentType' => $treatmentType->treatmentTypeHierarchy->Title ?? null,
                    'TreatmentSubType' => $treatmentType->subTreatmentTypeHierarchy->Title ?? null,
                    'TeethTreatment' => $treatmentType->TeethTreatment,
                    'TeethTreatmentNote' => $treatmentType->TeethTreatmentNote,
                    'TreatmentCost' => $treatmentType->TreatmentCost,
                    'Discount' => $treatmentType->Discount,
                    'Addition' => $treatmentType->Addition,
                ];
        }
        } else {
            $result['TreatmentSteps'] = [];
             $result['TreatmentType'] = [];
        }
        $result['children'] = [];
        if(!empty($this->children)) {
            $result['children'] = PatientTreatmentsDoneResource::collection($this->children);
        }

        return $result;
    }
}