<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables as YajraDataTables;

class ManageBankAccountController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = BankAccount::where('IsDeleted', false);

            return YajraDataTables::of($query)
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && $request->get('search') != '') {
                        $searchTerm = '%' . $request->get('search') . '%';
                        $query->where(function ($q) use ($searchTerm) {
                            $q->where('BankAccountName', 'like', $searchTerm)
                              ->orWhere('AccountNumber', 'like', $searchTerm)
                              ->orWhere('Branch', 'like', $searchTerm)
                              ->orWhere('City', 'like', $searchTerm);
                        });
                    }
                })
                ->editColumn('CreatedOn', function ($account) {
                    return $account->CreatedOn ? $account->CreatedOn->format('M d, Y') : 'N/A';
                })
                ->addColumn('action', function ($account) {
                    return view('admin.bank-accounts.actions', compact('account'))->render();
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.bank-accounts.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'BankAccountName' => 'required|string|max:255',
            'AccountNumber' => 'nullable|string|max:255',
            'Branch' => 'nullable|string|max:255',
            'City' => 'nullable|string|max:50',
        ]);

        try {
            $data = [
                'BankAccountName' => $request->BankAccountName,
                'AccountNumber' => $request->AccountNumber,
                'Branch' => $request->Branch,
                'City' => $request->City,
                'ClinicID' => Auth::user()->ClinicID ?? 'E403D9FF-A62D-463A-83D1-91C0EEEA2CD4',
                'IsDeleted' => false,
                'CreatedBy' => Auth::user()->UserID ?? 'System',
                'CreatedOn' => now(),
                'LastUpdatedBy' => Auth::user()->UserID ?? 'System',
                'LastUpdatedOn' => now(),
                'rowguid' => strtoupper(Str::uuid()->toString()),
            ];

            $account = BankAccount::create($data);

            Log::info('Bank account created successfully', ['id' => $account->BankAccountID]);

            return response()->json([
                'success' => true,
                'message' => 'Bank account created successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating bank account', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error creating bank account: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function edit($id)
    {
        $account = BankAccount::findOrFail($id);
        return response()->json($account);
    }

    public function update(Request $request, $id)
    {
        $account = BankAccount::findOrFail($id);

        $request->validate([
            'BankAccountName' => 'required|string|max:255',
            'AccountNumber' => 'nullable|string|max:255',
            'Branch' => 'nullable|string|max:255',
            'City' => 'nullable|string|max:50',
        ]);

        try {
            $account->update([
                'BankAccountName' => $request->BankAccountName,
                'AccountNumber' => $request->AccountNumber,
                'Branch' => $request->Branch,
                'City' => $request->City,
                'LastUpdatedBy' => Auth::user()->UserID ?? 'System',
                'LastUpdatedOn' => now(),
            ]);

            Log::info('Bank account updated successfully', ['id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Bank account updated successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating bank account', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating bank account: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $account = BankAccount::findOrFail($id);
            $account->update([
                'IsDeleted' => true,
                'LastUpdatedBy' => Auth::user()->UserID ?? 'System',
                'LastUpdatedOn' => now(),
            ]);

            Log::info('Bank account deleted successfully', ['id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Bank account deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting bank account', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting bank account: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No bank accounts selected for deletion',
                ], 422);
            }

            BankAccount::whereIn('BankAccountID', $ids)->update([
                'IsDeleted' => true,
                'LastUpdatedBy' => Auth::user()->UserID ?? 'System',
                'LastUpdatedOn' => now(),
            ]);

            Log::info('Bank accounts bulk deleted successfully', ['count' => count($ids)]);

            return response()->json([
                'success' => true,
                'message' => count($ids) . ' bank account(s) deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error bulk deleting bank accounts', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting bank accounts: ' . $e->getMessage(),
            ], 500);
        }
    }
}
