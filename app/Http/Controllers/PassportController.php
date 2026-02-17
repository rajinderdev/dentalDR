<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\PatientInvoice;
use App\Models\PatientReceipt;
use App\Models\PatientPackage;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PassportController extends Controller
{
    /**
     * Get merged financial passport for a patient (invoices + receipts)
     *
     * @param Request $request
     * @param string $patientId
     * @return \Illuminate\Http\JsonResponse
     */
    public function patientPassport(Request $request, $patientId = null)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        if (!$startDate) {
            $startDate = Carbon::today()->subYear()->toDateString();
        }
        if (!$endDate) {
            $endDate = Carbon::today()->toDateString();
        }

        // Fetch invoices
        $invoicesQuery = PatientInvoice::query();
        if ($patientId) {
            $invoicesQuery->where('PatientID', $patientId);
        } else {
            $invoicesQuery->leftJoin('Patient as p', 'PatientInvoices.PatientID', '=', 'p.PatientID');
        }
        $invoices = $invoicesQuery
            ->whereDate('InvoiceDate', '>=', $startDate)
            ->whereDate('InvoiceDate', '<=', $endDate)
            ->select([
                'InvoiceID as ID',
                'InvoiceDate as date',
                'InvoiceNumber as detail',
                'TreatmentTotalCost as credit',
                'PatientInvoices.Status as status',
                'TreatmentBalance as balance',
                $patientId ? DB::raw('NULL as patient_name') : DB::raw("CONCAT(p.Title, ' ', p.FirstName, ' ', p.LastName) as patient_name")
            ])
            ->get()
            ->map(function ($item) use ($patientId) {
                $item->type = 'invoice';
                $item->debit = null;
                if (!$patientId) {
                    $item->patient_name = $item->patient_name;
                }
                return $item;
            });

        // Fetch receipts
        $receiptsQuery = PatientReceipt::query();
        if ($patientId) {
            $receiptsQuery->where('PatientID', $patientId);
        } else {
            $receiptsQuery->leftJoin('Patient as p', 'PatientReceipts.PatientID', '=', 'p.PatientID');
        }
        $receipts = $receiptsQuery
            ->whereDate('ReceiptDate', '>=', $startDate)
            ->whereDate('ReceiptDate', '<=', $endDate)
            ->select([
                'ReceiptID as ID',
                'ReceiptDate as date',
                'ReceiptNumber as detail',
                'TreatmentPayment as debit',
                'BalanceAmount as balance',
                $patientId ? DB::raw('NULL as patient_name') : DB::raw("CONCAT(p.Title, ' ', p.FirstName, ' ', p.LastName) as patient_name")
            ])
            ->get()
            ->map(function ($item) use ($patientId) {
                $item->status = null; 
                $item->type = 'receipt';
                $item->credit = null;
                if (!$patientId) {
                    $item->patient_name = $item->patient_name;
                }
                return $item;
            });

        // Fetch patient packages
        $packagesQuery = PatientPackage::query();
        if ($patientId) {
            $packagesQuery->where('PatientID', $patientId);
        } else {
            $packagesQuery->leftJoin('Patient as p', 'patient_packages.PatientID', '=', 'p.PatientID');
        }
        $packages = $packagesQuery
            ->whereDate('CreatedOn', '>=', $startDate)
            ->whereDate('CreatedOn', '<=', $endDate)
            ->select([
                'PatientPackageID as ID',
                'CreatedOn as date',
                DB::raw("CONCAT('Package - ', PackageID) as detail"),
                'TotalCost as credit',
                'PaymentStatus as status',
                'AmountPaid as balance',
                $patientId ? DB::raw('NULL as patient_name') : DB::raw("CONCAT(p.Title, ' ', p.FirstName, ' ', p.LastName) as patient_name")
            ])
            ->get()
            ->map(function ($item) use ($patientId) {
                $item->type = 'package';
                $item->debit = null;
                if (!$patientId) {
                    $item->patient_name = $item->patient_name;
                }
                return $item;
            });
        // Fetch wallet transactions
        $walletTransactionsQuery = WalletTransaction::query();
        if ($patientId) {
            $walletTransactionsQuery->where('PatientID', $patientId);
        } else {
            $walletTransactionsQuery->leftJoin('Patient as p', 'wallet_transactions.PatientID', '=', 'p.PatientID');
        }
        $walletTransactions = $walletTransactionsQuery
            ->whereDate('CreatedOn', '>=', $startDate)
            ->whereDate('CreatedOn', '<=', $endDate)
            ->select([
                'TransactionID as ID',
                'CreatedOn as date',
                'Description as detail',
                DB::raw("CASE WHEN TransactionType = 'credit' THEN Amount ELSE NULL END as credit"),
                DB::raw("CASE WHEN TransactionType = 'debit' THEN Amount ELSE NULL END as debit"),
                'Status as status',
                'BalanceAfter as balance',
                $patientId ? DB::raw('NULL as patient_name') : DB::raw("CONCAT(p.Title, ' ', p.FirstName, ' ', p.LastName) as patient_name")
            ])
            ->get()
            ->map(function ($item) use ($patientId) {
                $item->type = 'wallet_transaction';
                if (!$patientId) {
                    $item->patient_name = $item->patient_name;
                }
                return $item;
            });

        // Merge and sort by date
        $merged = $invoices->concat($receipts)->concat($packages)->concat($walletTransactions)->sortByDesc(function ($item) {
            return $item->date;
        })->values();

        return response()->json([
            'success' => true,
            'data' => $merged,
        ]);
    }
}
