<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\WalletRequest;
use App\Http\Requests\WalletTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\WalletResource;
use App\Http\Resources\PatientReceiptResource;
use App\Models\Patient;
use App\Models\PatientWallet;
use App\Models\PatientReceipt;
use App\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class WalletController extends Controller
{
    /**
     * @var WalletService
     */
    protected $walletService;

    /**
     * Create a new controller instance.
     */
    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
        * @group Wallet
        *
        * @subgroup Wallets
        * @subgroupDescription WalletController handles patient wallet and credit note (advance payment) operations.
        *
     * Display a listing of wallets.
        *
        * @queryParam per_page int optional. Number of records per page. Example: 15
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = $request->input('per_page', 15);
        $wallets = PatientWallet::with(['patient', 'transactions'])
            ->where('IsDeleted', false)
            ->paginate($perPage);

        return WalletResource::collection($wallets);
    }

    /**
        * @group Wallet
        *
        * Create a wallet for a patient.
        *
        * @bodyParam PatientID string required. Patient UUID. Example: "00000000-0000-0000-0000-000000000000"
        * @bodyParam FamilyID string optional. Family UUID (if using family-shared wallet). Example: "00000000-0000-0000-0000-000000000000"
        * @bodyParam Currency string required. 3-letter ISO currency code. Example: "INR"
        * @bodyParam InitialBalance number optional. Initial wallet balance. Example: 1000
        * @bodyParam IsActive boolean optional. Example: true
     * Store a newly created wallet in storage.
     */
    public function store(WalletRequest $request): WalletResource
    {
        $validated = $request->validated();
        
        
        $wallet = $this->walletService->createWallet($validated);
        
        return new WalletResource($wallet->load(['patient', 'transactions']));
    }

    /**
     * Display the specified wallet.
     */
    public function show(PatientWallet $wallet): WalletResource
    {
        return new WalletResource($wallet->load(['patient', 'transactions']));
    }

    /**
     * Update the specified wallet in storage.
     */
    public function update(WalletRequest $request, PatientWallet $wallet): WalletResource
    {
        $validated = $request->validated();
        $validated['LastUpdatedBy'] = $request->user()?->UserID ?? 'system';
        
        $wallet = $this->walletService->updateWallet($wallet, $validated);
        
        return new WalletResource($wallet);
    }

    /**
     * Remove the specified wallet from storage.
     */
    public function destroy(PatientWallet $wallet): JsonResponse
    {
        $this->walletService->deleteWallet($wallet);
        
        return response()->json([
            'message' => 'Wallet deleted successfully',
        ], Response::HTTP_OK);
    }

    /**
        * @group Wallet
        *
        * Get wallet by patient ID (family-shared).
        *
        * If the patient has a FamilyID and a family wallet exists, it will return that wallet.
     * Get wallet by patient ID.
     */
    public function getByPatient(Patient $patient): WalletResource|JsonResponse
    {
        $wallet = $this->walletService->getWalletForPatient($patient);
        
        if (!$wallet) {
            return response()->json([
                'message' => 'No wallet found for this patient',
            ], Response::HTTP_NOT_FOUND);
        }
        
        return new WalletResource($wallet->load(['patient', 'transactions']));
    }

    /**
        * @group Wallet
        *
        * Get wallet summary for dashboard (family-shared).
        *
        * Returns the available wallet balance that can be used against future invoices.
        *
        * @urlParam patient string required. Patient UUID.
     * Get wallet balance/summary for a patient (family-shared when FamilyID exists).
     */
    public function summary(Patient $patient): JsonResponse
    {
        $wallet = $this->walletService->getWalletForPatient($patient);

        return response()->json([
            'patient_id' => $patient->PatientID,
            'family_id' => $patient->FamilyID,
            'wallet_id' => $wallet?->WalletID,
            'balance' => (float) ($wallet?->Balance ?? 0),
            'currency' => $wallet?->Currency ?? 'INR',
        ], Response::HTTP_OK);
    }

    /**
        * @group Wallet
        *
        * Create Credit Note (Advance Payment) and top-up wallet.
        *
        * This creates a PatientReceipts entry with IsCreditNote=true and credits the patient's (family) wallet.
        *
        * @urlParam patient string required. Patient UUID.
        * @bodyParam amount number required. Credit note amount. Example: 1500
        * @bodyParam clinic_id string optional. Clinic UUID. Example: "00000000-0000-0000-0000-000000000000"
        * @bodyParam receipt_number string optional. Manual receipt number. Example: "CN-0001"
        * @bodyParam receipt_date string optional. Date/time. Example: "2026-01-27 10:30:00"
        * @bodyParam mode_of_payment string optional. Example: "CASH"
        * @bodyParam receipt_notes string optional. Example: "Advance payment"
        * @bodyParam payment_notes string optional. Example: "Received in cash"
     * Create an advance payment (Credit Note) for the clinic and top-up the patient's (family) wallet.
     */
    public function createCreditNote(Request $request, Patient $patient): JsonResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'clinic_id' => ['nullable', 'string'],
            'receipt_number' => ['nullable', 'string', 'max:50'],
            'receipt_date' => ['nullable', 'date'],
            'mode_of_payment' => ['nullable', 'string', 'max:50'],
            'receipt_notes' => ['nullable', 'string'],
            'payment_notes' => ['nullable', 'string'],
        ]);

        $amount = (float) $validated['amount'];
        $createdBy = $request->user()?->UserID ?? 'system';

        $receiptNo = ((int) (PatientReceipt::max('ReceiptNo') ?? 0)) + 1;
        $receiptNumber = $validated['receipt_number'] ?? (string) $receiptNo;

        $receiptDate = !empty($validated['receipt_date'])
            ? Carbon::parse($validated['receipt_date'])
            : now();

        $payload = [
            'ClinicID' => $validated['clinic_id'] ?? null,
            'ReceiptNo' => $receiptNo,
            'ReceiptNumber' => $receiptNumber,
            'ReceiptDate' => $receiptDate,
            'ModeofPayment' => $validated['mode_of_payment'] ?? 'CASH',
            'ReceiptNotes' => $validated['receipt_notes'] ?? 'Credit note',
            'PaymentNotes' => $validated['payment_notes'] ?? null,
            'IsDeleted' => false,
            'IsCancelled' => false,
        ];

        $result = $this->walletService->createCreditNoteReceiptAndTopUp(
            $patient,
            $payload,
            $amount,
            $createdBy
        );

        return response()->json([
            'message' => 'Credit note created and wallet topped up',
            'wallet' => new WalletResource($result['wallet']),
            'receipt' => new PatientReceiptResource($result['receipt']),
            'transaction' => new TransactionResource($result['transaction']),
        ], Response::HTTP_CREATED);
    }

    /**
        * @group Wallet
        *
        * List Credit Notes (Advance Payments).
        *
        * Returns all credit note receipts for the patient. If patient belongs to a family, it includes family members.
        *
        * @urlParam patient string required. Patient UUID.
        * @queryParam per_page int optional. Number of records per page. Example: 15
     * List credit notes (advance payments) receipts for a patient (includes family members when FamilyID exists).
     */
    public function listCreditNotes(Request $request, Patient $patient): AnonymousResourceCollection
    {
        $perPage = (int) $request->input('per_page', 15);

        $query = PatientReceipt::query()
            ->where('IsDeleted', false)
            ->where('IsCreditNote', true)
            ->orderBy('ReceiptDate', 'desc');

        if (!empty($patient->FamilyID)) {
            $familyPatientIds = Patient::where('FamilyID', $patient->FamilyID)->pluck('PatientID');
            $query->whereIn('PatientID', $familyPatientIds);
        } else {
            $query->where('PatientID', $patient->PatientID);
        }

        return PatientReceiptResource::collection($query->paginate($perPage));
    }

    /**
     * Get wallet transactions.
     */
    public function getTransactions(
        PatientWallet $wallet,
        Request $request
    ): AnonymousResourceCollection {
        $type = $request->input('type');
        $perPage = $request->input('per_page', 15);
        
        $transactions = $this->walletService->getWalletTransactions(
            $wallet->WalletID, 
            $type, 
            $perPage
        );
        
        return TransactionResource::collection($transactions);
    }

    /**
     * Create a new transaction for the wallet.
     */
    public function createTransaction(
        WalletTransactionRequest $request, 
        PatientWallet $wallet
    ): TransactionResource {
        $validated = $request->validated();
        $validated['CreatedBy'] = $request->user()?->UserID ?? 'system';
        
        $transaction = $this->walletService->createTransaction(
            array_merge($validated, ['WalletID' => $wallet->WalletID])
        );
        
        return new TransactionResource($transaction->load('wallet'));
    }

    /**
     * Get a specific transaction.
     */
    public function getTransaction(
        PatientWallet $wallet, 
        string $transactionId
    ): TransactionResource {
        $transaction = $this->walletService->getTransactionById($transactionId);
        
        // Verify the transaction belongs to the wallet
        if ($transaction->WalletID !== $wallet->WalletID) {
            abort(Response::HTTP_NOT_FOUND, 'Transaction not found for this wallet');
        }
        
        return new TransactionResource($transaction->load('wallet'));
    }
}
