<?php
namespace App\Services\Reports;

use App\Models\Provider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BillingReportService
{
    public function getCreditReport(array $filters): array
    {
        $start = $filters['start_date'] ?? null;
        $end = $filters['end_date'] ?? null;
        if (!$start || !$end) return [];

        $user = Auth::user();
        $doctorProviderId = null;
        if ($user && $user->role && strtolower($user->role->RoleName) === 'doctor') {
            $doctorProviderId = Provider::where('UserID', $user->UserID)->value('ProviderID');
            if (!$doctorProviderId) {
                return [];
            }
        }

        $invoices = \App\Models\PatientInvoice::query()
            ->leftJoin('Patient as p', 'PatientInvoices.PatientID', '=', 'p.PatientID')
            ->leftJoin('PatientTreatmentsDone as ptd', 'PatientInvoices.PatientTreatmentDoneID', '=', 'ptd.PatientTreatmentDoneID')
            ->select([
                'PatientInvoices.InvoiceDate',
                'PatientInvoices.InvoiceNumber',
                'PatientInvoices.InvoiceCodePrefix',
                'PatientInvoices.TreatmentTotalCost',
                'PatientInvoices.Notes',
                'p.FirstName',
                'p.LastName',
                'p.Title',
                'p.PatientCode',
            ])
            ->whereBetween('PatientInvoices.InvoiceDate', [$start, $end])
            ->when($doctorProviderId, fn($q) => $q->where('ptd.ProviderID', $doctorProviderId))
            ->get();

        $result = [];
        foreach ($invoices as $inv) {
            $prefix = $inv->InvoiceCodePrefix;
            $patientName = trim(($inv->Title ?? '') . ' ' . ($inv->FirstName ?? '') . ' ' . ($inv->LastName ?? ''));
            $patientCode = $inv->PatientCode ?? '';
            $result[] = [
                'voucher_type' => 'sales',
                'voucher_date' => $inv->InvoiceDate,
                'voucher_number' => $inv->InvoiceNumber,
                'reference_number' => $patientCode,
                'patient_name' => $patientName,
                'cost_centre' => $prefix,
                'debit_ledger' => 'Patient-' . $prefix,
                'debit_amount' => $inv->TreatmentTotalCost,
                'credit_ledger' => 'Sales-' . $prefix,
                'credit_amount' => $inv->TreatmentTotalCost,
                'narration' => $inv->Notes,
            ];
        }
        return $result;
    }

    public function getDebitReport(array $filters): array
    {
        $start = $filters['start_date'] ?? null;
        $end = $filters['end_date'] ?? null;
        if (!$start || !$end) return [];

        $user = Auth::user();
        $doctorProviderId = null;
        if ($user && $user->role && strtolower($user->role->RoleName) === 'doctor') {
            $doctorProviderId = Provider::where('UserID', $user->UserID)->value('ProviderID');
            if (!$doctorProviderId) {
                return [];
            }
        }

        $receipts = \App\Models\PatientReceipt::query()
            ->leftJoin('Patient as p', 'PatientReceipts.PatientID', '=', 'p.PatientID')
            ->leftJoin('PatientTreatmentsDone as ptd', 'PatientReceipts.PatientTreatmentDoneId', '=', 'ptd.PatientTreatmentDoneID')
            ->select([
                'PatientReceipts.ReceiptDate',
                'PatientReceipts.ReceiptNumber',
                'PatientReceipts.ReceiptCodePrefix',
                'PatientReceipts.InvoicedAmount',
                'PatientReceipts.ReceiptNotes',
                'p.FirstName',
                'p.LastName',
                'p.Title',
                'p.PatientCode',
            ])
            ->whereBetween('PatientReceipts.ReceiptDate', [$start, $end])
            ->when($doctorProviderId, fn($q) => $q->where('ptd.ProviderID', $doctorProviderId))
            ->get();

        $result = [];
        foreach ($receipts as $rec) {
            $prefix = $rec->ReceiptCodePrefix;
            $patientName = trim(($rec->Title ?? '') . ' ' . ($rec->FirstName ?? '') . ' ' . ($rec->LastName ?? ''));
            $patientCode = $rec->PatientCode ?? '';
            $result[] = [
                'voucher_type' => 'receipt',
                'voucher_date' => $rec->ReceiptDate,
                'voucher_number' => $rec->ReceiptNumber,
                'reference_number' => $patientCode,
                'patient_name' => $patientName,
                'cost_centre' => $prefix,
                'debit_ledger' => 'Patient-' . $prefix,
                'debit_amount' => $rec->InvoicedAmount,
                'credit_ledger' => 'Sales-' . $prefix,
                'credit_amount' => $rec->InvoicedAmount,
                'narration' => $rec->ReceiptNotes,
            ];
        }
        return $result;
    }
}
