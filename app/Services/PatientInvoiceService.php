<?php

namespace App\Services;

use App\Models\PatientInvoice;
use Illuminate\Support\Facades\Log; // Assuming you have a PatientInvoice model
use App\Models\PatientInvoicesDetail; // Import PatientInvoicesDetail model
use App\Http\Resources\PatientInvoiceResource; // Assuming you have a resource for Patient Invoice
use App\Models\Patient;
use App\Models\Provider;
use App\Models\PatientTreatmentsDone;
use App\Helpers\EntityDataHelper;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Str;

class PatientInvoiceService
{
    /**
     * Get a paginated list of Patient Invoices.
     *
     * @param int $perPage
     * @return array
     */
    public function getInvoices($patient = null, int $perPage = 50, array $filters = []): array
    {
        $user = Auth::user();
        $doctorProviderId = null;
        if ($user && $user->role && strtolower($user->role->RoleName) === 'doctor') {
            $doctorProviderId = Provider::where('UserID', $user->UserID)->value('ProviderID');
            if (!$doctorProviderId) {
                $empty = new LengthAwarePaginator([], 0, $perPage);
                return [
                    'invoices' => $empty,
                    'pagination' => [
                        'current_page' => $empty->currentPage(),
                        'per_page' => $empty->perPage(),
                        'total' => $empty->total(),
                    ]
                ];
            }
        }

        $query = PatientInvoice::query()
            ->when($patient, function ($q) use ($patient) {
                $q->where('PatientID', $patient);
            })
            ->when(!empty($filters['start_date']), function ($q) use ($filters) {
                // Use >= full datetime to keep index usage
                $q->where('InvoiceDate', '>=', $filters['start_date'] . ' 00:00:00');
            })
            ->when(!empty($filters['end_date']), function ($q) use ($filters) {
                $q->where('InvoiceDate', '<=', $filters['end_date'] . ' 23:59:59');
            })
            ->when(!empty($filters['PatientTreatmentDoneID']), function ($q) use ($filters) {
                $q->where('PatientTreatmentDoneID', $filters['PatientTreatmentDoneID']);
            });

        // Enforce doctor visibility via treatment-provider mapping.
        if ($doctorProviderId) {
            $query->whereExists(function ($sub) use ($doctorProviderId) {
                $sub->from('PatientTreatmentsDone')
                    ->whereColumn('PatientTreatmentsDone.PatientTreatmentDoneID', 'PatientInvoices.PatientTreatmentDoneID')
                    ->where('PatientTreatmentsDone.ProviderID', $doctorProviderId);
            });
        }

        // $data = $query->paginate($perPage);
        $data = $query->with(['patient', 'patientTreatmentsType'])->orderBy('CreatedOn','desc')->paginate($perPage);

        return [
            'invoices' => $data, 
            'pagination' => [
                'current_page' => $data->currentPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ]
        ];
    }
    /**
     * Get a paginated list of Patient Invoices.
     *
     * @param int $perPage
     * @return array
     */
    public function unpaidInvoices($patient = null, int $perPage = 50, array $filters = [], $patientTreatmentDoneID = null): array
    {
        $user = Auth::user();
        $doctorProviderId = null;
        if ($user && $user->role && strtolower($user->role->RoleName) === 'doctor') {
            $doctorProviderId = Provider::where('UserID', $user->UserID)->value('ProviderID');
            if (!$doctorProviderId) {
                $empty = new LengthAwarePaginator([], 0, $perPage);
                return [
                    'invoices' => $empty,
                    'pagination' => [
                        'current_page' => $empty->currentPage(),
                        'per_page' => $empty->perPage(),
                        'total' => $empty->total(),
                    ]
                ];
            }
        }

        $query = PatientInvoice::query()->where('Status', '!=', 1)
            ->when($patientTreatmentDoneID, function ($q) use ($patientTreatmentDoneID) {
                $q->where('PatientTreatmentDoneID', $patientTreatmentDoneID);
            })
            ->when($patient, function ($q) use ($patient) {
                $q->where('PatientID', $patient);
            })
            ->when(!empty($filters['start_date']), function ($q) use ($filters) {
                // Use >= full datetime to keep index usage
                $q->where('InvoiceDate', '>=', $filters['start_date'] . ' 00:00:00');
            })
            ->when(!empty($filters['end_date']), function ($q) use ($filters) {
                $q->where('InvoiceDate', '<=', $filters['end_date'] . ' 23:59:59');
            })
            ->when(!empty($filters['PatientTreatmentDoneID']), function ($q) use ($filters) {
                $q->where('PatientTreatmentDoneID', $filters['PatientTreatmentDoneID']);
            });

        if ($doctorProviderId) {
            $query->whereExists(function ($sub) use ($doctorProviderId) {
                $sub->from('PatientTreatmentsDone')
                    ->whereColumn('PatientTreatmentsDone.PatientTreatmentDoneID', 'PatientInvoices.PatientTreatmentDoneID')
                    ->where('PatientTreatmentsDone.ProviderID', $doctorProviderId);
            });
        }

        // $data = $query->paginate($perPage);
        $data = $query->with(['patient', 'patientTreatmentsType'])->orderBy('CreatedOn','desc')->paginate($perPage);

        return [
            'invoices' => $data, 
            'pagination' => [
                'current_page' => $data->currentPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ]
        ];
    }
    /**
     * Get a paginated list of Patient Invoices.
     *
     * @param int $perPage
     * @return array
     */
    public function paidInvoices($patient = null, int $perPage = 50, array $filters = [], $patientTreatmentDoneID = null): array
    {
        $user = Auth::user();
        $doctorProviderId = null;
        if ($user && $user->role && strtolower($user->role->RoleName) === 'doctor') {
            $doctorProviderId = Provider::where('UserID', $user->UserID)->value('ProviderID');
            if (!$doctorProviderId) {
                $empty = new LengthAwarePaginator([], 0, $perPage);
                return [
                    'invoices' => $empty,
                    'pagination' => [
                        'current_page' => $empty->currentPage(),
                        'per_page' => $empty->perPage(),
                        'total' => $empty->total(),
                    ]
                ];
            }
        }

        $query = PatientInvoice::query()
            ->where(function ($q) {
                $q->whereNull('Status')->orWhere('Status', 0);
            })
            ->where('IsDeleted', 0);
        if ($patient) {
            $query->where('PatientID', $patient);
        }

        if ($patientTreatmentDoneID) {
            $query->where('PatientTreatmentDoneID', $patientTreatmentDoneID);
        }

        if (!empty($filters['start_date'])) {
            $startDate = $filters['start_date'] . ' 00:00:00';
            $query->where('InvoiceDate', '>=', $startDate);
        }

        if (!empty($filters['end_date'])) {
            $endDate = $filters['end_date'] . ' 23:59:59';
            $query->where('InvoiceDate', '<=', $endDate);
        }

        if (!empty($filters['PatientTreatmentDoneID'])) {
            $childPatientTreatmentDoneIDS = PatientTreatmentsDone::where('ParentPatientTreatmentDoneID',$filters['PatientTreatmentDoneID'])->pluck('PatientTreatmentDoneID')->toArray();
          
            $query->where('PatientTreatmentDoneID', $filters['PatientTreatmentDoneID'])->orWhereIn('PatientTreatmentDoneID', $childPatientTreatmentDoneIDS);
        }
        if (!empty($filters['PatientTreatmentDoneIDS'])) {
            $childPatientTreatmentDoneIDS = PatientTreatmentsDone::whereIn('ParentPatientTreatmentDoneID',$filters['PatientTreatmentDoneIDS'])->pluck('PatientTreatmentDoneID')->toArray();
            $query->whereIn('PatientTreatmentDoneID', array_merge($filters['PatientTreatmentDoneIDS'], $childPatientTreatmentDoneIDS));
        }
        if (!empty($filters['InvoiceID'])) {
            $query->where('InvoiceID', $filters['InvoiceID']);
        }
        if (!empty($filters['InvoiceIDS'])) {
           $query->whereIn('InvoiceID', $filters['InvoiceIDS']);
        }
        if ($doctorProviderId) {
            $query->whereExists(function ($sub) use ($doctorProviderId) {
                $sub->from('PatientTreatmentsDone')
                    ->whereColumn('PatientTreatmentsDone.PatientTreatmentDoneID', 'PatientInvoices.PatientTreatmentDoneID')
                    ->where('PatientTreatmentsDone.ProviderID', $doctorProviderId);
            });
        }

        $data = $query->with(['patient', 'patientTreatmentsType'])
            ->orderBy('CreatedOn', 'desc')
            ->paginate($perPage);

        return [
            'invoices' => $data, 
            'pagination' => [
                'current_page' => $data->currentPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ]
        ];
    }
    public function createInvoice(array $data): PatientInvoice
    {
        return DB::transaction(function () use ($data) {
            $data['Status'] = ($data['TreatmentTotalPayment'] ?? 0) == ($data['TreatmentTotalCost'] ?? 0) ? 1 : 0;

            $invoiceDetails = $data['invoice_details'] ?? [];
            unset($data['invoice_details']);

            // 🚀 Direct create without eager loading relation
            $invoice = PatientInvoice::create($data);

            // Reduce relation loading
            if (!empty($invoice->PatientTreatmentDoneID)) {
                $treatment = PatientTreatmentsDone::select([
                    'TreatmentAddition', 'TreatmentDiscount', 'TreatmentTax'
                ])->find($invoice->PatientTreatmentDoneID);

                if ($treatment) {
                    $invoice->fill([
                        'TreatmentAddition' => $treatment->TreatmentAddition,
                        'TreatmentDiscount' => $treatment->TreatmentDiscount,
                        'TreatmentTax' => $treatment->TreatmentTax,
                    ])->save();
                }
            }

            // Mark as paid if no payment
            if (empty($invoice->TreatmentTotalPayment)) {
                $invoice->update(['Status' => 1]);
            }

            // Create default invoice details in bulk
            if (empty($invoiceDetails)) {
                $invoiceDetails = [[
                    'InvoiceID' => $invoice->InvoiceID,
                    'PatientTreatmentDoneID' => $invoice->PatientTreatmentDoneID,
                    'TreatmentCost' => $invoice->TreatmentCost,
                    'TreatmentDiscount' => $invoice->TreatmentDiscount,
                    'TreatmentTotalCost' => $invoice->TreatmentTotalCost,
                    'TreatmentTotalPayment' => $invoice->TreatmentTotalPayment,
                    'TreatmentBalance' => $invoice->TreatmentBalance,
                    'TreatmentAddition' => $invoice->TreatmentAddition,
                    'TreatmentTax' => $invoice->TreatmentTax,
                    'TreatmentSummary' => $invoice->Notes,
                    'TreatmentDate' => $data['TreatmentDate'] ?? now(),
                ]];
            }

            if (empty($invoiceDetails)) {
                $treatmentDate = $invoice->patientTreatmentDone 
                    ? $invoice->patientTreatmentDone->TreatmentDate 
                    : now()->toDateTimeString();
                    
                $invoiceDetails = [[
                    'InvoiceID' => $invoice->InvoiceID,
                    'PatientTreatmentDoneID' => $invoice->PatientTreatmentDoneID,
                    'TreatmentCost' => $invoice->TreatmentCost,
                    'TreatmentDiscount' => $invoice->TreatmentDiscount,
                    'TreatmentTotalCost' => $invoice->TreatmentTotalCost,
                    'TreatmentTotalPayment' => $invoice->TreatmentTotalPayment,
                    'TreatmentBalance' => $invoice->TreatmentBalance,
                    'TreatmentAddition' => $invoice->TreatmentAddition,
                    'TreatmentTax' => $invoice->TreatmentTax,
                    'TreatmentSummary' => $invoice->Notes,
                    'TreatmentDate' => $treatmentDate,
                ]];
            }
            
            // Create invoice details
            $this->createInvoiceDetails($invoice->InvoiceID, $invoiceDetails);

            // Return only what is needed
            return $invoice;
        });
    }

        /**
     * Create invoice details for a given invoice
     * 
     * @param string $invoiceId
     * @param array $detailsData
     * @return void
     */
    private function createInvoiceDetails(string $invoiceId, array $detailsData): void
    {
       
        foreach ($detailsData as $detail) {
            // Prepare detail data with common creation fields
            
            $detail['InvoiceID'] = $invoiceId;
            $detail = EntityDataHelper::prepareForCreation($detail);
       
            // Create the invoice detail
            $data = PatientInvoicesDetail::create($detail);
        }
    }
    public function updateInvoice(string $pi, array $data): PatientInvoice
    {
        $pi = PatientInvoice::find($pi);
        if($pi){
       
                $balance = $pi->TreatmentTotalCost-$data['TreatmentTotalPayment'];
                $data['TreatmentBalance'] = $balance;
                $data['TreatmentBalance']= $data['TreatmentBalance'] - (int)$data['TreatmentDiscount'];
                $data['TreatmentBalance']= $data['TreatmentBalance'] + (int)$data['TreatmentAddition'];
        }
        $pi->update($data); // Update the invoice with the provided data
        $invoice =PatientInvoice::find($pi->InvoiceID);
      
        $invoiceDetails = [
                    'InvoiceID' => $invoice->InvoiceID,
                    'PatientTreatmentDoneID' => $invoice->PatientTreatmentDoneID,
                    'TreatmentCost' => $invoice->TreatmentCost,
                    'TreatmentDiscount' => $invoice->TreatmentDiscount,
                    'TreatmentTotalCost' => $invoice->TreatmentTotalCost,
                    'TreatmentTotalPayment' => $invoice->TreatmentTotalPayment,
                    'TreatmentBalance' => $invoice->TreatmentBalance,
                    'TreatmentAddition' => $invoice->TreatmentAddition,
                    'TreatmentTax' => $invoice->TreatmentTax,
                    'TreatmentSummary' => $invoice->Notes,
                    'TreatmentDate' => $invoice->TreatmentDate,
                ];
                 $invoiceDetail = PatientInvoicesDetail::where('InvoiceID', $invoice->InvoiceID)->first();
                
                 if ($invoiceDetail) {
                     $invoiceDetail->update($invoiceDetails);
                 } else {
                     PatientInvoicesDetail::create($invoiceDetails);
                 }

        
        $pi->fresh();
        return $pi;
    }
}