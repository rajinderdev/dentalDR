<?php

namespace App\Http\Controllers;

use App\Models\TreatmentTypeHierarchy;
use App\Models\ECGMTreatmentTypeHierarchy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables as YajraDataTables;

class ManageTreatmentHierarchyController extends Controller
{
    private function getClinicId()
    {
        return Auth::user()->ClinicID ?? 'E403D9FF-A62D-463A-83D1-91C0EEEA2CD4';
    }

    private function getUserId()
    {
        return Auth::user()->UserID ?? 'System';
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = TreatmentTypeHierarchy::
                where(function ($q) {
                    $q->where('IsDeleted', false)->orWhereNull('IsDeleted');
                })
                ->where(function ($q) {
                    $q->where('ParentTreatmentTypeID', '00000000-0000-0000-0000-000000000000')
                      ->orWhereNull('ParentTreatmentTypeID');
                })
                ->select([
                    'TreatmentTypeID',
                    'ClinicID',
                    'Title',
                    'Description',
                    'GeneralTreatmentCost',
                    'SpecialistTreatmentCost',
                    'DoctorIncentiveAmount',
                    'CreatedOn',
                ]);

            return YajraDataTables::of($query)
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && $request->get('search') != '') {
                        $searchTerm = '%' . $request->get('search') . '%';
                        $query->where(function ($q) use ($searchTerm) {
                            $q->where('Title', 'like', $searchTerm)
                              ->orWhere('Description', 'like', $searchTerm);
                        });
                    }
                })
                ->addColumn('StepsCount', function ($row) {
                    return TreatmentTypeHierarchy::
                        where('ParentTreatmentTypeID', $row->TreatmentTypeID)
                        ->where(function ($q) {
                            $q->where('IsDeleted', false)->orWhereNull('IsDeleted');
                        })
                        ->count();
                })
                ->editColumn('GeneralTreatmentCost', function ($row) {
                    return $row->GeneralTreatmentCost ? number_format($row->GeneralTreatmentCost, 2) : '0.00';
                })
                ->editColumn('SpecialistTreatmentCost', function ($row) {
                    return $row->SpecialistTreatmentCost ? number_format($row->SpecialistTreatmentCost, 2) : '0.00';
                })
                ->editColumn('DoctorIncentiveAmount', function ($row) {
                    return $row->DoctorIncentiveAmount ? number_format($row->DoctorIncentiveAmount, 2) : '0.00';
                })
                ->editColumn('CreatedOn', function ($row) {
                     return $row->CreatedOn ? $row->CreatedOn->format('M d, Y') : 'N/A';
                })
                ->addColumn('action', function ($row) {
                    return view('admin.treatment-hierarchy.actions', ['treatment' => $row])->render();
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $treatmentTypes = TreatmentTypeHierarchy::
                where(function ($q) {
                    $q->where('IsDeleted', false)->orWhereNull('IsDeleted');
                })
                ->where(function ($q) {
                    $q->where('ParentTreatmentTypeID', '00000000-0000-0000-0000-000000000000')
                      ->orWhereNull('ParentTreatmentTypeID');
                })
                ->select([
                    'TreatmentTypeID',
                    'ClinicID',
                    'Title',
                    'Description',
                    'GeneralTreatmentCost',
                    'SpecialistTreatmentCost',
                    'DoctorIncentiveAmount',
                    'CreatedOn',
                ])->orderBy('CreatedOn', 'desc')->get();
        return view('admin.treatment-hierarchy.index', compact('treatmentTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Title' => 'required|string|max:255',
            'Description' => 'nullable|string|max:500',
            'GeneralTreatmentCost' => 'nullable|numeric|min:0',
            'SpecialistTreatmentCost' => 'nullable|numeric|min:0',
            'DoctorIncentiveAmount' => 'nullable|numeric|min:0',
            ]);

        try {
            $clinicId = $this->getClinicId();
            $userId = $this->getUserId();

            TreatmentTypeHierarchy::create([
                'TreatmentTypeID' => strtoupper(Str::uuid()->toString()),
                'ClinicID' => $clinicId,
                'Title' => $request->Title,
                'Description' => $request->Description,
                'ParentTreatmentTypeID' => $request->ParentTreatmentTypeID ?? '00000000-0000-0000-0000-000000000000',
                'GeneralTreatmentCost' => $request->GeneralTreatmentCost ?? 0,
                'SpecialistTreatmentCost' => $request->SpecialistTreatmentCost ?? 0,
                'DoctorIncentiveAmount' => $request->DoctorIncentiveAmount ?? 0,
                'TreatmentSpecialityTypeID' => 1,
                'IsDeleted' => false,
                'CreatedOn' => now(),
                'CreatedBy' => $userId,
                'LastUpdatedOn' => now(),
                'LastUpdatedBy' => $userId,
                'rowguid' => strtoupper(Str::uuid()->toString()),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Treatment type created successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating treatment type', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error creating treatment type: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function edit($id)
    {
        $clinicId = $this->getClinicId();
        $treatment = TreatmentTypeHierarchy::where('TreatmentTypeID', $id)
            ->firstOrFail();

        return response()->json($treatment);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Title' => 'required|string|max:255',
            'Description' => 'nullable|string|max:500',
            'GeneralTreatmentCost' => 'nullable|numeric|min:0',
            'SpecialistTreatmentCost' => 'nullable|numeric|min:0',
            'DoctorIncentiveAmount' => 'nullable|numeric|min:0',
        ]);

        try {
            $clinicId = $this->getClinicId();
            $userId = $this->getUserId();

            $treatment = TreatmentTypeHierarchy::where('TreatmentTypeID', $id)
                ->firstOrFail();

            $treatment->update([
                'Title' => $request->Title,
                'Description' => $request->Description,
                'GeneralTreatmentCost' => $request->GeneralTreatmentCost ?? 0,
                'SpecialistTreatmentCost' => $request->SpecialistTreatmentCost ?? 0,
                'DoctorIncentiveAmount' => $request->DoctorIncentiveAmount ?? 0,
                'LastUpdatedOn' => now(),
                'LastUpdatedBy' => $userId,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Treatment type updated successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating treatment type', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error updating treatment type: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $clinicId = $this->getClinicId();
            $userId = $this->getUserId();

            // Soft delete the treatment type
            TreatmentTypeHierarchy::where('TreatmentTypeID', $id)
                ->where('ClinicID', $clinicId)
                ->update([
                    'IsDeleted' => true,
                    'LastUpdatedBy' => $userId,
                    'LastUpdatedOn' => now(),
                ]);

            // Also soft delete all child steps
            TreatmentTypeHierarchy::where('ParentTreatmentTypeID', $id)
                ->where('ClinicID', $clinicId)
                ->update([
                    'IsDeleted' => true,
                    'LastUpdatedBy' => $userId,
                    'LastUpdatedOn' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Treatment type deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting treatment type', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error deleting treatment type: ' . $e->getMessage(),
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
                    'message' => 'No treatment types selected for deletion',
                ], 422);
            }

            $clinicId = $this->getClinicId();
            $userId = $this->getUserId();

            // Soft delete selected treatments
            TreatmentTypeHierarchy::whereIn('TreatmentTypeID', $ids)
                ->where('ClinicID', $clinicId)
                ->update([
                    'IsDeleted' => true,
                    'LastUpdatedBy' => $userId,
                    'LastUpdatedOn' => now(),
                ]);

            // Soft delete all children of selected treatments
            TreatmentTypeHierarchy::whereIn('ParentTreatmentTypeID', $ids)
                ->where('ClinicID', $clinicId)
                ->update([
                    'IsDeleted' => true,
                    'LastUpdatedBy' => $userId,
                    'LastUpdatedOn' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => count($ids) . ' treatment type(s) deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error bulk deleting treatment types', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error deleting treatment types: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getSteps($id)
    {
        $clinicId = $this->getClinicId();

        $steps = TreatmentTypeHierarchy::
            where('ParentTreatmentTypeID', $id)
            ->where(function ($q) {
                $q->where('IsDeleted', false)->orWhereNull('IsDeleted');
            })
            ->orderBy('CreatedOn', 'desc')
            ->get(['TreatmentTypeID', 'Title', 'Description', 'GeneralTreatmentCost', 'SpecialistTreatmentCost','DoctorIncentiveAmount','CreatedOn']);

        return response()->json($steps);
    }

    public function addStep(Request $request)
    {
        $request->validate([
            'ParentTreatmentTypeID' => 'required|string',
            'Title' => 'required|string|max:255',
            'Description' => 'nullable|string|max:500',
        ]);

        try {
            $clinicId = $this->getClinicId();
            $userId = $this->getUserId();

            TreatmentTypeHierarchy::create([
                'TreatmentTypeID' => strtoupper(Str::uuid()->toString()),
                'ClinicID' => $clinicId,
                'Title' => $request->Title,
                'Description' => $request->Description,
                'ParentTreatmentTypeID' => $request->ParentTreatmentTypeID,
                'GeneralTreatmentCost' => 0,
                'SpecialistTreatmentCost' => 0,
                'TreatmentSpecialityTypeID' => 1,
                'IsDeleted' => false,
                'CreatedOn' => now(),
                'CreatedBy' => $userId,
                'LastUpdatedOn' => now(),
                'LastUpdatedBy' => $userId,
                'rowguid' => strtoupper(Str::uuid()->toString()),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Step added successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error adding step', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error adding step: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function removeStep($id)
    {
        try {
            $userId = $this->getUserId();

            TreatmentTypeHierarchy::where('TreatmentTypeID', $id)
                ->update([
                    'IsDeleted' => true,
                    'LastUpdatedBy' => $userId,
                    'LastUpdatedOn' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Step removed successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error removing step', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error removing step: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function searchMasterTreatments(Request $request)
    {
        $clinicId = $this->getClinicId();
        $search = $request->get('q', '');

        $query = ECGMTreatmentTypeHierarchy::where('ClinicID', $clinicId)
            ->where(function ($q) {
                $q->where('IsDeleted', false)->orWhereNull('IsDeleted');
            })
            ->where(function ($q) {
                $q->where('ParentTreatmentTypeID', '00000000-0000-0000-0000-000000000000')
                  ->orWhereNull('ParentTreatmentTypeID');
            });

        if ($search) {
            $query->where('Title', 'like', '%' . $search . '%');
        }

        $treatments = $query->orderBy('Title')
            ->limit(50)
            ->get(['TreatmentTypeID', 'Title', 'Description']);

        return response()->json($treatments);
    }

    public function addFromMaster(Request $request)
    {
        $request->validate([
            'ParentTreatmentTypeID' => 'required|string',
            'MasterTreatmentTypeID' => 'required|string',
        ]);

        try {
            $clinicId = $this->getClinicId();
            $userId = $this->getUserId();

            $master = ECGMTreatmentTypeHierarchy::where('TreatmentTypeID', $request->MasterTreatmentTypeID)
                ->where('ClinicID', $clinicId)
                ->first();

            if (!$master) {
                return response()->json([
                    'success' => false,
                    'message' => 'Master treatment not found',
                ], 404);
            }

            // Check if step already exists under this parent
            $existing = TreatmentTypeHierarchy::where('ClinicID', $clinicId)
                ->where('ParentTreatmentTypeID', $request->ParentTreatmentTypeID)
                ->where('Title', $master->Title)
                ->where(function ($q) {
                    $q->where('IsDeleted', false)->orWhereNull('IsDeleted');
                })
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'This treatment step already exists under the selected parent',
                ], 422);
            }

            TreatmentTypeHierarchy::create([
                'TreatmentTypeID' => strtoupper(Str::uuid()->toString()),
                'ClinicID' => $clinicId,
                'Title' => $master->Title,
                'Description' => $master->Description,
                'ParentTreatmentTypeID' => $request->ParentTreatmentTypeID,
                'GeneralTreatmentCost' => $master->GeneralTreatmentCost ?? 0,
                'SpecialistTreatmentCost' => $master->SpecialistTreatmentCost ?? 0,
                'TreatmentSpecialityTypeID' => $master->TreatmentSpecialityTypeID ?? 1,
                'IsDeleted' => false,
                'CreatedOn' => now(),
                'CreatedBy' => $userId,
                'LastUpdatedOn' => now(),
                'LastUpdatedBy' => $userId,
                'rowguid' => strtoupper(Str::uuid()->toString()),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Treatment step added from master list',
            ]);
        } catch (\Exception $e) {
            Log::error('Error adding from master', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error adding treatment: ' . $e->getMessage(),
            ], 500);
        }
    }
}
