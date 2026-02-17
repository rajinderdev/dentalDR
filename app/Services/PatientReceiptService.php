<?php

namespace App\Services;

use App\Models\PatientReceipt; // Assuming you have a PatientReceipt model
use App\Models\PatientReceiptsDetail; // Assuming you have a PatientReceipt model
use App\Models\PatientInvoicesDetail; // Assuming you have a PatientReceipt model
use App\Http\Resources\PatientReceiptResource; // Assuming you have a resource for Patient Receipt
use App\Models\Patient;
use App\Models\Provider;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use App\Helpers\EntityDataHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\PatientInvoice;
use Illuminate\Pagination\LengthAwarePaginator;

class PatientReceiptService
{
    /**
     * Get a paginated list of Patient Receipts.
     *
     * @param int $perPage
     * @return array
     */
    public function getPatientReceipts($patient = null, int $perPage = 50, array $filters = []): array
    {
        $query = PatientReceipt::query()->with(['receiptDetails', 'patientInvoice']);

        $user = Auth::user();
        $doctorProviderId = null;
        if ($user && $user->role && strtolower($user->role->RoleName) === 'doctor') {
            $doctorProviderId = Provider::where('UserID', $user->UserID)->value('ProviderID');
            if (!$doctorProviderId) {
                $empty = new LengthAwarePaginator([], 0, $perPage);
                return [
                    'patient_receipts' => $empty,
                    'pagination' => [
                        'current_page' => $empty->currentPage(),
                        'per_page' => $empty->perPage(),
                        'total' => $empty->total(),
                        'last_page' => $empty->lastPage(),
                    ]
                ];
            }

            // PatientReceipts table doesn't reliably contain ProviderID; enforce via treatment.
            $query->whereExists(function ($sub) use ($doctorProviderId) {
                $sub->from('PatientTreatmentsDone')
                    ->whereColumn('PatientTreatmentsDone.PatientTreatmentDoneID', 'PatientReceipts.PatientTreatmentDoneId')
                    ->where('PatientTreatmentsDone.ProviderID', $doctorProviderId);
            });
        }

        if ($patient) {
            $query->where('PatientID', $patient);
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('ReceiptDate', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('ReceiptDate', '<=', $filters['end_date']);
        }

        $data = $query->orderBy('ReceiptDate', 'desc')->paginate($perPage);

        return [
            'patient_receipts' => $data,
            'pagination' => [
                'current_page' => $data->currentPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'last_page' => $data->lastPage(),
            ]
        ];
    }

   
    public function createPatientReceipt(array $data, array $invoiceArray): PatientReceipt
    {
        // ✅ Find related invoice (throws 404 if not found)
        $invoice = PatientInvoice::findOrFail($data['InvoiceID']);

        // Doctors may only create receipts against their own invoices/treatments.
        $user = Auth::user();
        if ($user && $user->role && strtolower($user->role->RoleName) === 'doctor') {
            $doctorProviderId = Provider::where('UserID', $user->UserID)->value('ProviderID');
            if (!$doctorProviderId) {
                throw new \Exception('Doctor provider record not found');
            }

            $treatment = $invoice->patientTreatmentDone;
            if (!$treatment || $treatment->ProviderID !== $doctorProviderId) {
                throw new \Exception('Unauthorized');
            }
        }
        // ✅ Ensure PatientID is present (fallback to invoice)
        if (empty($data['PatientID'])) {
            $data['PatientID'] = $invoice->PatientID;
        }

        $patient = \App\Models\Patient::find($data['PatientID']);
        if (!$patient) {
            throw new \Exception("Patient with ID {$data['PatientID']} not found");
        }

        // ✅ Normalize ReceiptNo
        if (isset($data['ReceiptNo']) && is_numeric($data['ReceiptNo'])) {
            $data['ReceiptNo'] = (int) $data['ReceiptNo'];
        } elseif (!empty($data['ReceiptNumber']) && is_numeric($data['ReceiptNumber'])) {
            $data['ReceiptNo'] = (int) $data['ReceiptNumber'];
        } elseif (!empty($data['ReceiptNo'])) {
            // Extract digits (e.g., RCP-001 → 1)
            $digits = preg_replace('/\D+/', '', (string) $data['ReceiptNo']);
            $data['ReceiptNo'] = $digits !== '' ? (int) $digits : null;
        } else {
            // Auto-generate next number
            $data['ReceiptNo'] = ((int) (PatientReceipt::max('ReceiptNo') ?? 0)) + 1;
        }

        // ✅ Normalize dates
        foreach (['ReceiptDate', 'ChequeDate'] as $dateField) {
            if (!empty($data[$dateField])) {
                try {
                    $data[$dateField] = Carbon::parse($data[$dateField]);
                } catch (\Throwable $e) {
                    $data[$dateField] = null;
                }
            } else {
                $data[$dateField] = null;
            }
        }

        // ✅ Ensure numeric fields are float
        foreach (['TreatmentPayment', 'WalletAmount', 'InvoicedAmount', 'BalanceAmount'] as $amountKey) {
            if (isset($data[$amountKey])) {
                $data[$amountKey] = is_numeric($data[$amountKey]) ? (float) $data[$amountKey] : null;
            }
        }

        // ✅ Use transaction for safety
        return DB::transaction(function () use ($data, $invoice, $invoiceArray) {

            $dataToPersist = EntityDataHelper::prepareForCreation($data);

            // Create receipt
            $receipt = PatientReceipt::create($dataToPersist);

            // ✅ Create receipt detail
            PatientReceiptsDetail::create([
                'ReceiptID' => $receipt->ReceiptID,
                'InvoiceID' => $data['InvoiceID'],
                'PatientTreatmentDoneID' => $receipt? $receipt->PatientTreatmentDoneID : null,
                'AmountPaid' => $data['TreatmentPayment'] ?? 0,
                'IsDeleted' => false,
                'CreatedOn' => now(),
                'CreatedBy' => $data['CreatedBy'] ?? null,
                'LastUpdatedOn' => now(),
                'LastUpdatedBy' => $data['LastUpdatedBy'] ?? null,
            ]);
            $collectedAmount = PatientReceipt::where('InvoiceID', $data['InvoiceID'])
                ->where('BalanceAmount', 0)
                ->where('IsDeleted', 0)
                ->where('IsCancelled', 0)
                ->first();
            if($collectedAmount|| $invoice->TreatmentBalance==$invoice->invoiceDiscount){
                    PatientInvoice::where('InvoiceID', $data['InvoiceID'])->update([
                        'Status' => 1,
                    ]);
            }
           
            // ✅ Convert invoice array keys to PascalCase and update invoice
            $invoiceArr = Self::convertKeysToPascalCase($invoiceArray);
            $invoiceArr1 = EntityDataHelper::prepareForUpdate($invoiceArr);
            $invoice->update($invoiceArr1);
            if (!empty($invoiceArr['InvoiceDetails'])) {
                foreach($invoiceArr['InvoiceDetails'] as $invoicesDetail){
                    $invoicesDetailExist = PatientInvoicesDetail::findOrFail($invoicesDetail['Id']);
                    $invoicesDetail1 = EntityDataHelper::prepareForUpdate($invoicesDetail);
                    $invoicesDetailExist->update($invoicesDetail1);
                }
                
            }
           

            return $receipt;
        });
    }

    /**
     * Directly add an amount to an existing invoice and update its totals.
     */
    public function addAmountToInvoice(string $invoiceId, float $amount, ?string $lastUpdatedBy = null): PatientInvoice
    {
        $invoice = PatientInvoice::findOrFail($invoiceId);

        $increment = max(0.0, (float) $amount);
        $newTotalPayment = (float) ($invoice->TreatmentTotalPayment ?? 0) + $increment;
        $treatmentTotalCost = (float) ($invoice->TreatmentTotalCost ?? 0);
        $newBalance = max(0.0, $treatmentTotalCost - $newTotalPayment);

        $invoice->update([
            'TreatmentTotalPayment' => $newTotalPayment,
            'TreatmentBalance' => $newBalance,
            'LastUpdatedOn' => now(),
            'LastUpdatedBy' => $lastUpdatedBy,
        ]);

        return $invoice->fresh();
    }


    public function updatePatientReceipt(PatientReceipt $patientReceipt, array $data): PatientReceipt
    {
        $patientReceipt->update($data);
        $patientReceipt->fresh();
        return $patientReceipt;
    }
    public function convertKeysToPascalCase(array $array)
    {
        $converted = [];

        foreach ($array as $key => $value) {
            // Convert snake_case to PascalCase
            $newKey = str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));

            // Recursively convert nested arrays
            if (is_array($value)) {
                $converted[$newKey] = Self::convertKeysToPascalCase($value);
            } else {
                $converted[$newKey] = $value;
            }
        }

        return $converted;
    }
   
}
