<?php

namespace App\Http\Controllers;

use App\Models\Drug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables as YajraDataTables;

class ManageMedicineController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Drug::where('IsDeleted', false);

            return YajraDataTables::of($query)
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && $request->get('search') != '') {
                        $searchTerm = '%' . $request->get('search') . '%';
                        $query->where(function ($q) use ($searchTerm) {
                            $q->where('GenericName', 'like', $searchTerm)
                              ->orWhere('Contraindications', 'like', $searchTerm)
                              ->orWhere('Precautions', 'like', $searchTerm);
                        });
                    }
                })
                ->editColumn('CreatedOn', function ($medicine) {
                    return $medicine->CreatedOn ? $medicine->CreatedOn->format('M d, Y') : 'N/A';
                })
                ->editColumn('LastUpdatedOn', function ($medicine) {
                    return $medicine->LastUpdatedOn ? $medicine->LastUpdatedOn->format('M d, Y') : 'N/A';
                })
                ->addColumn('action', function ($medicine) {
                    return view('admin.medicines.actions', compact('medicine'))->render();
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.medicines.index');
    }

    public function create()
    {
        return view('admin.medicines.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'GenericName' => 'required|string|max:100',
            'Contraindications' => 'nullable|string|max:500',
            'Interactions' => 'nullable|string|max:500',
            'AdverseEffects' => 'nullable|string|max:500',
            'OverdozeManagement' => 'nullable|string|max:500',
            'Precautions' => 'nullable|string|max:500',
            'PatientAlerts' => 'nullable|string|max:200',
            'OtherInfo' => 'nullable|string|max:500',
        ]);

        try {
            $medicineData = [
                'GenericName' => $request->GenericName,
                'Contraindications' => $request->Contraindications,
                'Interactions' => $request->Interactions,
                'AdverseEffects' => $request->AdverseEffects,
                'OverdozeManagement' => $request->OverdozeManagement,
                'Precautions' => $request->Precautions,
                'PatientAlerts' => $request->PatientAlerts,
                'OtherInfo' => $request->OtherInfo,
                'ClinicID' => Auth::user()->ClinicID ?? 'E403D9FF-A62D-463A-83D1-91C0EEEA2CD4',
                'IsDeleted' => false,
                'CreatedBy' => Auth::user()->UserID ?? 'System',
                'CreatedOn' => now(),
                'LastUpdatedBy' => Auth::user()->UserID ?? 'System',
                'LastUpdatedOn' => now(),
                'rowguid' => strtoupper(Str::uuid()->toString()),
            ];

            $medicine = Drug::create($medicineData);

            Log::info('Medicine created successfully', ['drug_id' => $medicine->DrugId]);

            return response()->json([
                'success' => true,
                'message' => 'Medicine created successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating medicine', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error creating medicine: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function edit($id)
    {
        $medicine = Drug::findOrFail($id);
        return view('admin.medicines.edit', compact('medicine'));
    }

    public function update(Request $request, $id)
    {
        $medicine = Drug::findOrFail($id);

        $request->validate([
            'GenericName' => 'required|string|max:100',
            'Contraindications' => 'nullable|string|max:500',
            'Interactions' => 'nullable|string|max:500',
            'AdverseEffects' => 'nullable|string|max:500',
            'OverdozeManagement' => 'nullable|string|max:500',
            'Precautions' => 'nullable|string|max:500',
            'PatientAlerts' => 'nullable|string|max:200',
            'OtherInfo' => 'nullable|string|max:500',
        ]);

        try {
            $updateData = [
                'GenericName' => $request->GenericName,
                'Contraindications' => $request->Contraindications,
                'Interactions' => $request->Interactions,
                'AdverseEffects' => $request->AdverseEffects,
                'OverdozeManagement' => $request->OverdozeManagement,
                'Precautions' => $request->Precautions,
                'PatientAlerts' => $request->PatientAlerts,
                'OtherInfo' => $request->OtherInfo,
                'LastUpdatedBy' => Auth::user()->UserID ?? 'System',
                'LastUpdatedOn' => now(),
            ];

            $medicine->update($updateData);

            Log::info('Medicine updated successfully', ['drug_id' => $medicine->DrugId]);

            return response()->json([
                'success' => true,
                'message' => 'Medicine updated successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating medicine', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating medicine: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $medicine = Drug::findOrFail($id);
            $medicine->update([
                'IsDeleted' => true,
                'LastUpdatedBy' => Auth::user()->UserID ?? 'System',
                'LastUpdatedOn' => now(),
            ]);

            Log::info('Medicine deleted successfully', ['drug_id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Medicine deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting medicine', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting medicine: ' . $e->getMessage(),
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
                    'message' => 'No medicines selected for deletion',
                ], 422);
            }

            Drug::whereIn('DrugId', $ids)->update([
                'IsDeleted' => true,
                'LastUpdatedBy' => Auth::user()->UserID ?? 'System',
                'LastUpdatedOn' => now(),
            ]);

            Log::info('Medicines bulk deleted successfully', ['count' => count($ids)]);

            return response()->json([
                'success' => true,
                'message' => count($ids) . ' medicine(s) deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error bulk deleting medicines', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting medicines: ' . $e->getMessage(),
            ], 500);
        }
    }
}
