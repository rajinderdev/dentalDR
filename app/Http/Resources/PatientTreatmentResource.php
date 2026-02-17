<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Models\PatientReceipt;
use App\Models\PatientInvoice;
use App\Models\PatientTreatmentsDone;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
class PatientTreatmentResource extends JsonResource
{
    public function toArray($request)
    {
            $balanceData = [
                'TreatmentPayment' => 0,
                'finalBalance' => 0,
                'AllTreatmentCollectedPayment' => 0,
                'AllTreatmentBalnce' => 0,
                'allTreatmentCost' => 0
            ];

        if($this->IsCompleted == 1){
             $treatmentIds = collect([$this->PatientTreatmentDoneID])
            ->merge(optional($this->children)->pluck('PatientTreatmentDoneID'))
            ->toArray();
            $balanceData = $this->calculateBalanceData($treatmentIds);
        }
       
        $children = $this->children && $this->children->isNotEmpty() 
            ? PatientTreatmentResource::collection($this->children) 
            : [];
        return [
            'id'                              => $this->PatientTreatmentDoneID,
            'patient_id'                      => $this->PatientID,
            'patient'                         => $this->formatPatientName(),
            'patient_email'                   => $this->patient->EmailAddress1 ?? null,
            'patient_gender'                  => $this->patient->Gender ?? null,
            'patient_phone'                   => $this->patient->MobileNumber ?? null,
            'patient_image'                   => $this->patient?->ImagePath ? asset($this->patient->ImagePath) : null,
            'provider_id'                     => $this->ProviderID,
            'provider'                        => $this->doctor->ProviderName ?? null,
            'treatment_cost'                  => $this->TreatmentCost??0,
            'treatment_balance'               => $this->TreatmentBalance??0,
            'treatment_payment'               => $this->TreatmentPayment??0,
            'treatment_total_cost'               => $this->TreatmentTotalCost??0,
            'treatment_date'                  => $this->TreatmentDate,
            'TreatmentDiscount'               => $this->TreatmentDiscount??0,
            'TreatmentAddition'               => $this->TreatmentAddition??0,
            'TeethTreatmentNote'               => $this->TeethTreatmentNote??null,
            'token_number'                    => $this->waiting_area->TokenNumber ?? null,
            'reason'                          => null,
            'apt_type'                        => null,
            'completion_time'                 => $this->CompletionTime,
            'arrival_time'                    =>  $this->waiting_area && $this->waiting_area->ArrivalTime ? \Carbon\Carbon::parse($this->waiting_area->ArrivalTime)->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s') : null,
            'waiting_area_flag'               => $this->WaitingAreaFlag,
            'amount_to_be_collected'          => $this->IsCompleted == 1?$balanceData['TreatmentPayment']:0,
            'final_balance'                   => $this->IsCompleted == 1?$balanceData['finalBalance']:0,
            'all_treatment_collected_payment' => $this->IsCompleted == 1?$balanceData['AllTreatmentCollectedPayment']:0,
            'all_treatment_balance'           => $this->IsCompleted == 1?$balanceData['AllTreatmentBalnce']:0,
            'all_treatment_cost'              => $this->IsCompleted == 1?$balanceData['allTreatmentCost']:0,    
            'children' => $children,
        ];   

    }

    private function formatPatientName()
    {
        return $this->patient
            ? trim("{$this->patient->Title} {$this->patient->FirstName} {$this->patient->LastName}")
            : null;
    }

    private function calculateBalanceData(array $treatmentIds)
    {
        $patientId = $this->PatientID;
        $treatments = PatientTreatmentsDone::where('PatientID', $patientId)->where('ParentPatientTreatmentDoneID','00000000-0000-0000-0000-000000000000')->get();
      
        $alltreatmentIds = [];
        $allTreatmentCost = 0;
        // dd($treatments->toArray());
        foreach ($treatments as $treatment) {
            $Childs = PatientTreatmentsDone::where('ParentPatientTreatmentDoneID', $treatment->PatientTreatmentDoneID);
            $discount = $Childs->sum('TreatmentDiscount');
            $addition = $Childs->sum('TreatmentAddition');
            $totaldiscount = $discount + $treatment->TreatmentDiscount;
            $totaladdition = $addition + $treatment->TreatmentAddition;
            $totalTreatmentCost = $treatment->TreatmentCost - $totaldiscount + $totaladdition;
                $latestChild = $Childs->orderByDesc('AddedOn')
                ->first();
            $alltreatmentIds[] = $latestChild
                ? $latestChild->PatientTreatmentDoneID
                : $treatment->PatientTreatmentDoneID;
                  $allTreatmentCost =  $allTreatmentCost+$totalTreatmentCost;
        }

        $receiptQuery = PatientReceipt::where('PatientID', $patientId);
        $invoiceQuery = PatientInvoice::query();

        $allInvoiceIds = $receiptQuery->pluck('InvoiceID')->toArray();
        $totalReceiptBalance = $receiptQuery->sum('BalanceAmount');
        $treatmentReceiptBalance = $receiptQuery->whereIn('PatientTreatmentDoneID', $treatmentIds)->sum('BalanceAmount');

        $invoicePaymentQuery = clone $invoiceQuery;
        $invoicePaymentQuery->whereNotIn('InvoiceID', $allInvoiceIds);

        $allInvoicePayment = $invoicePaymentQuery->where('PatientID', $patientId)->sum('TreatmentTotalPayment');
        $invoicePayment = $invoicePaymentQuery->where('PatientID', $patientId)->whereIn('PatientTreatmentDoneID', $treatmentIds)->sum('TreatmentTotalPayment');

        $latestInvoice = (clone $invoiceQuery)
            ->where('PatientID', $patientId)
            ->whereIn('PatientTreatmentDoneID', $treatmentIds)
            ->orderByDesc('CreatedOn')
            ->first();

        $AllTreatmentBalnce = (clone $invoiceQuery)
            ->where('PatientID', $patientId)
            ->whereIn('PatientTreatmentDoneID', $alltreatmentIds)->sum('TreatmentBalance');

        return [
            'finalBalance'               => $latestInvoice->TreatmentBalance ?? 0,
            'TreatmentPayment'           => $invoicePayment + $treatmentReceiptBalance,
            'AllTreatmentCollectedPayment' => $allInvoicePayment + $totalReceiptBalance,
            'AllTreatmentBalnce' => (int)$AllTreatmentBalnce,
            'allTreatmentCost' => (int)$allTreatmentCost,
        ];
    }
}
