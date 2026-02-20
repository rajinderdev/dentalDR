<?php

namespace App\Http\Controllers;

use App\Models\PrescriptionTemplateMaster;
use App\Models\PrescriptionTemplate;
use App\Models\Drug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables as YajraDataTables;

class ManageRxTemplateController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = PrescriptionTemplateMaster::where('IsDeleted', false);

            return YajraDataTables::of($query)
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && $request->get('search') != '') {
                        $searchTerm = '%' . $request->get('search') . '%';
                        $query->where(function ($q) use ($searchTerm) {
                            $q->where('PrescriptionTemplateName', 'like', $searchTerm)
                              ->orWhere('PrescriptionTemplateDesc', 'like', $searchTerm)
                              ->orWhere('PrescriptionNote', 'like', $searchTerm);
                        });
                    }
                })
                ->editColumn('CreatedOn', function ($template) {
                    return $template->CreatedOn ? $template->CreatedOn->format('M d, Y') : 'N/A';
                })
                ->editColumn('LastUpdatedOn', function ($template) {
                    return $template->LastUpdatedOn ? $template->LastUpdatedOn->format('M d, Y') : 'N/A';
                })
                ->addColumn('medicines_count', function ($template) {
                    return PrescriptionTemplate::where('PrescriptionTemplateMasterID', $template->PrescriptionTemplateMasterID)
                        ->where('IsDeleted', false)
                        ->count();
                })
                ->addColumn('action', function ($template) {
                    return view('admin.rx-templates.actions', compact('template'))->render();
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.rx-templates.index');
    }

    public function create()
    {
        $medicines = Drug::where('IsDeleted', false)->orderBy('GenericName')->get();
        return view('admin.rx-templates.create', compact('medicines'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'PrescriptionTemplateName' => 'required|string|max:255',
            'PrescriptionTemplateDesc' => 'nullable|string|max:500',
            'PrescriptionNote' => 'nullable|string|max:500',
            'template_medicines' => 'nullable|array',
            'template_medicines.*.MedicineName' => 'required|string',
            'template_medicines.*.Frequency' => 'nullable|string|max:250',
            'template_medicines.*.Dosage' => 'nullable|string|max:250',
            'template_medicines.*.Duration' => 'nullable|string|max:250',
            'template_medicines.*.DrugNote' => 'nullable|string',
        ]);

        try {
            $clinicId = Auth::user()->ClinicID ?? 'E403D9FF-A62D-463A-83D1-91C0EEEA2CD4';
            $userId = Auth::user()->UserID ?? 'System';

            $masterData = [
                'PrescriptionTemplateName' => $request->PrescriptionTemplateName,
                'PrescriptionTemplateDesc' => $request->PrescriptionTemplateDesc,
                'PrescriptionNote' => $request->PrescriptionNote,
                'ClinicID' => $clinicId,
                'IsDeleted' => false,
                'CreatedBy' => $userId,
                'CreatedOn' => now(),
                'LastUpdatedBy' => $userId,
                'LastUpdatedOn' => now(),
            ];

            $master = PrescriptionTemplateMaster::create($masterData);

            // Create child template medicines
            if ($request->has('template_medicines')) {
                foreach ($request->template_medicines as $index => $med) {
                    if (empty($med['MedicineName'])) continue;

                    PrescriptionTemplate::create([
                        'ClinicId' => $clinicId,
                        'TemplateName' => $request->PrescriptionTemplateName,
                        'MedicineId' => $med['MedicineId'] ?? null,
                        'MedicineName' => $med['MedicineName'],
                        'Frequency' => $med['Frequency'] ?? null,
                        'Dosage' => $med['Dosage'] ?? null,
                        'Duration' => $med['Duration'] ?? null,
                        'DrugNote' => $med['DrugNote'] ?? null,
                        'SequenceOrder' => $index + 1,
                        'PrescriptionTemplateMasterID' => $master->PrescriptionTemplateMasterID,
                        'IsDeleted' => false,
                        'CreatedBy' => $userId,
                        'CreatedOn' => now(),
                        'LastUpdatedBy' => $userId,
                        'LastUpdatedOn' => now(),
                    ]);
                }
            }

            Log::info('Rx-Template created successfully', ['id' => $master->PrescriptionTemplateMasterID]);

            return response()->json([
                'success' => true,
                'message' => 'Rx-Template created successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating Rx-Template', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error creating Rx-Template: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function edit($id)
    {
        $template = PrescriptionTemplateMaster::findOrFail($id);
        $templateMedicines = PrescriptionTemplate::where('PrescriptionTemplateMasterID', $id)
            ->where('IsDeleted', false)
            ->orderBy('SequenceOrder')
            ->get();
        $medicines = Drug::where('IsDeleted', false)->orderBy('GenericName')->get();

        return view('admin.rx-templates.edit', compact('template', 'templateMedicines', 'medicines'));
    }

    public function update(Request $request, $id)
    {
        $template = PrescriptionTemplateMaster::findOrFail($id);

        $request->validate([
            'PrescriptionTemplateName' => 'required|string|max:255',
            'PrescriptionTemplateDesc' => 'nullable|string|max:500',
            'PrescriptionNote' => 'nullable|string|max:500',
            'template_medicines' => 'nullable|array',
            'template_medicines.*.MedicineName' => 'required|string',
            'template_medicines.*.Frequency' => 'nullable|string|max:250',
            'template_medicines.*.Dosage' => 'nullable|string|max:250',
            'template_medicines.*.Duration' => 'nullable|string|max:250',
            'template_medicines.*.DrugNote' => 'nullable|string',
        ]);

        try {
            $clinicId = Auth::user()->ClinicID ?? 'E403D9FF-A62D-463A-83D1-91C0EEEA2CD4';
            $userId = Auth::user()->UserID ?? 'System';

            $template->update([
                'PrescriptionTemplateName' => $request->PrescriptionTemplateName,
                'PrescriptionTemplateDesc' => $request->PrescriptionTemplateDesc,
                'PrescriptionNote' => $request->PrescriptionNote,
                'LastUpdatedBy' => $userId,
                'LastUpdatedOn' => now(),
            ]);

            // Soft-delete existing child medicines
            PrescriptionTemplate::where('PrescriptionTemplateMasterID', $id)
                ->where('IsDeleted', false)
                ->update([
                    'IsDeleted' => true,
                    'LastUpdatedBy' => $userId,
                    'LastUpdatedOn' => now(),
                ]);

            // Re-create child template medicines
            if ($request->has('template_medicines')) {
                foreach ($request->template_medicines as $index => $med) {
                    if (empty($med['MedicineName'])) continue;

                    PrescriptionTemplate::create([
                        'ClinicId' => $clinicId,
                        'TemplateName' => $request->PrescriptionTemplateName,
                        'MedicineId' => $med['MedicineId'] ?? null,
                        'MedicineName' => $med['MedicineName'],
                        'Frequency' => $med['Frequency'] ?? null,
                        'Dosage' => $med['Dosage'] ?? null,
                        'Duration' => $med['Duration'] ?? null,
                        'DrugNote' => $med['DrugNote'] ?? null,
                        'SequenceOrder' => $index + 1,
                        'PrescriptionTemplateMasterID' => $id,
                        'IsDeleted' => false,
                        'CreatedBy' => $userId,
                        'CreatedOn' => now(),
                        'LastUpdatedBy' => $userId,
                        'LastUpdatedOn' => now(),
                    ]);
                }
            }

            Log::info('Rx-Template updated successfully', ['id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Rx-Template updated successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating Rx-Template', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating Rx-Template: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $template = PrescriptionTemplateMaster::findOrFail($id);
            $userId = Auth::user()->UserID ?? 'System';

            $template->update([
                'IsDeleted' => true,
                'LastUpdatedBy' => $userId,
                'LastUpdatedOn' => now(),
            ]);

            // Also soft-delete child medicines
            PrescriptionTemplate::where('PrescriptionTemplateMasterID', $id)
                ->where('IsDeleted', false)
                ->update([
                    'IsDeleted' => true,
                    'LastUpdatedBy' => $userId,
                    'LastUpdatedOn' => now(),
                ]);

            Log::info('Rx-Template deleted successfully', ['id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Rx-Template deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting Rx-Template', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting Rx-Template: ' . $e->getMessage(),
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
                    'message' => 'No templates selected for deletion',
                ], 422);
            }

            $userId = Auth::user()->UserID ?? 'System';

            PrescriptionTemplateMaster::whereIn('PrescriptionTemplateMasterID', $ids)->update([
                'IsDeleted' => true,
                'LastUpdatedBy' => $userId,
                'LastUpdatedOn' => now(),
            ]);

            // Also soft-delete child medicines
            PrescriptionTemplate::whereIn('PrescriptionTemplateMasterID', $ids)
                ->where('IsDeleted', false)
                ->update([
                    'IsDeleted' => true,
                    'LastUpdatedBy' => $userId,
                    'LastUpdatedOn' => now(),
                ]);

            Log::info('Rx-Templates bulk deleted successfully', ['count' => count($ids)]);

            return response()->json([
                'success' => true,
                'message' => count($ids) . ' template(s) deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error bulk deleting Rx-Templates', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting templates: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function searchMedicines(Request $request)
    {
        $search = $request->get('q', '');
        $medicines = Drug::where('IsDeleted', false)
            ->where('GenericName', 'like', '%' . $search . '%')
            ->orderBy('GenericName')
            ->limit(20)
            ->get(['DrugId', 'GenericName']);

        return response()->json($medicines);
    }
}
