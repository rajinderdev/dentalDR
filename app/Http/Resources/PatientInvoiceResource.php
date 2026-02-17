<?php

namespace App\Http\Resources;

use App\Models\PatientReceipt;
use App\Models\PatientTreatmentsDone;
use App\Models\PatientInvoice;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientInvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $finalBalance = null;
        $TreatmentPayment = null;
        if ($this->PatientTreatmentDoneID) {
           
            $PatientTreatmentDone = PatientTreatmentsDone::where('PatientTreatmentDoneID', $this->PatientTreatmentDoneID)->first();
            if($PatientTreatmentDone && $PatientTreatmentDone->ParentPatientTreatmentDoneID !='00000000-0000-0000-0000-000000000000'){
                $treatmentIds = PatientTreatmentsDone::where('ParentPatientTreatmentDoneID', $PatientTreatmentDone->ParentPatientTreatmentDoneID)->pluck('PatientTreatmentDoneID')->toArray();
                
                $treatmentIds = array_merge($treatmentIds, [$PatientTreatmentDone->ParentPatientTreatmentDoneID]);
            }
            else{
                 $treatmentIds = PatientTreatmentsDone::where('ParentPatientTreatmentDoneID', $this->PatientTreatmentDoneID)->pluck('PatientTreatmentDoneID')->toArray();
                $treatmentIds = array_merge($treatmentIds, [$this->PatientTreatmentDoneID]);
            }
       
             $parentBalance = PatientReceipt::whereIn('PatientTreatmentDoneID', $treatmentIds)
                ->sum('BalanceAmount');
            $invoiceIds = PatientReceipt::whereIn('PatientTreatmentDoneID', $treatmentIds)
                ->pluck('InvoiceID')->toArray();
            $invoiceBalance = PatientInvoice::whereIn('PatientTreatmentDoneID', $treatmentIds)->whereNotIn('InvoiceID', $invoiceIds)->sum('TreatmentTotalPayment');
             $balanceInvoice = PatientInvoice::whereIn('PatientTreatmentDoneID', $treatmentIds)
            ->orderBy('CreatedOn', 'desc')
            ->first();
            $invoiceDiscount = PatientInvoice::whereIn('PatientTreatmentDoneID', $treatmentIds)->sum('TreatmentDiscount');
            $invoiceAddition = PatientInvoice::whereIn('PatientTreatmentDoneID', $treatmentIds)->whereNotIn('InvoiceID', $invoiceIds)->sum('TreatmentAddition');
            $finalBalance = $balanceInvoice ? $balanceInvoice->TreatmentBalance-$invoiceDiscount+$invoiceAddition:0-$invoiceDiscount+$invoiceAddition;
            $TreatmentPayment = ($invoiceBalance+$parentBalance);
           
        }
        return [
            'id' => $this->InvoiceID,
            'clinic_id' => $this->ClinicID,
            'invoice_no' => $this->InvoiceNo,
            'invoice_number' => $this->InvoiceNumber,
            'manual_invoice_no' => $this->ManualInvoiceNo,
            'invoice_code_prefix' => $this->InvoiceCodePrefix,
            'invoice_date' => $this->InvoiceDate,
            'patient_id' => $this->PatientID,
            'patient_fullname' => $this->patient
                ? trim(implode(' ', array_filter([
                    $this->patient->Title ?? null,
                    $this->patient->FirstName ?? null,
                    $this->patient->LastName ?? null,
                ])))
                : null,
            'patient_treatment_done_id' => $this->PatientTreatmentDoneID,
            'treatment_cost' => $this->TreatmentCost??0,
            'treatment_addition' => $this->TreatmentAddition??0,
            'treatment_discount' => $this->TreatmentDiscount??0,
            'TreatmentDiscount' => (float)($this->TreatmentDiscount ?? 0),
            'treatment_tax' => $this->TreatmentTax ??0,
            'treatment_total_cost' => $this->TreatmentTotalCost??0,
            'treatment_total_payment' => $this->TreatmentTotalPayment??0,
            'treatment_balance' => $this->TreatmentBalance??0,
            'is_deleted' => $this->IsDeleted,
            'is_cancelled' => $this->IsCancelled,
            'cancellation_notes' => $this->CancellationNotes,
            'status' => $this->Status?$this->Status:0,
            'created_on' => $this->CreatedOn,
            'created_by' => $this->CreatedBy,
            'last_updated_by' => $this->LastUpdatedBy,
            'last_updated_on' => $this->LastUpdatedOn,
            'notes' => $this->Notes,
            'rowguid' => $this->rowguid,
            // Add related invoice details (one-to-many)
            'invoice_details' => PatientInvoicesDetailResource::collection($this->invoiceDetails),
            'treatment_type' => $this->patientTreatmentsType,
            // Add related invoice RB (one-to-many)
            'invoice_rb' => PatientInvoicesRBResource::collection($this->invoiceRB),
            'LastReceiptNo' => (((int) (PatientReceipt::max('ReceiptNo') ?? 0)) + 1),
            'PatientTreatmentDoneBalance' => $finalBalance,
            'AmountToBeCollected' => $TreatmentPayment,
        ];
    }
}