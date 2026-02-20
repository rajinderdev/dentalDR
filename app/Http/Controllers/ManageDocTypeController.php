<?php

namespace App\Http\Controllers;

use App\Models\PatientDOCFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables as YajraDataTables;

class ManageDocTypeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = PatientDOCFolder::where('IsDeleted', false);

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
                ->addColumn('action', function ($docType) {
                    return view('admin.doc-types.actions', compact('docType'))->render();
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.doc-types.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'Title' => 'required|string|max:255',
            'Description' => 'nullable|string|max:500',
        ]);

        try {
            $data = [
                'Title' => $request->Title,
                'Description' => $request->Description,
                'ClinicID' => Auth::user()->ClinicID ?? 'E403D9FF-A62D-463A-83D1-91C0EEEA2CD4',
                'ParentFolderId' => 0,
                'IsDeleted' => false,
                'CreatedBy' => Auth::user()->UserID ?? 'System',
                'CreatedOn' => now(),
                'LastUpdatedBy' => Auth::user()->UserID ?? 'System',
                'LastUpdatedOn' => now(),
                'FolderPath' => $request->Title,
                'PartitionId' => 1,
                'RowGuid' => strtoupper(Str::uuid()->toString()),
                'Owner' => Auth::user()->email ?? Auth::user()->UserID ?? 'System',
            ];

            $docType = PatientDOCFolder::create($data);

            Log::info('Document type created successfully', ['id' => $docType->FolderId]);

            return response()->json([
                'success' => true,
                'message' => 'Document type created successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating document type', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error creating document type: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function edit($id)
    {
        $docType = PatientDOCFolder::findOrFail($id);
        return response()->json($docType);
    }

    public function update(Request $request, $id)
    {
        $docType = PatientDOCFolder::findOrFail($id);

        $request->validate([
            'Title' => 'required|string|max:255',
            'Description' => 'nullable|string|max:500',
        ]);

        try {
            $docType->update([
                'Title' => $request->Title,
                'Description' => $request->Description,
                'FolderPath' => $request->Title,
                'LastUpdatedBy' => Auth::user()->UserID ?? 'System',
                'LastUpdatedOn' => now(),
            ]);

            Log::info('Document type updated successfully', ['id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Document type updated successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating document type', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating document type: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $docType = PatientDOCFolder::findOrFail($id);
            $docType->update([
                'IsDeleted' => true,
                'LastUpdatedBy' => Auth::user()->UserID ?? 'System',
                'LastUpdatedOn' => now(),
            ]);

            Log::info('Document type deleted successfully', ['id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Document type deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting document type', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting document type: ' . $e->getMessage(),
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
                    'message' => 'No document types selected for deletion',
                ], 422);
            }

            PatientDOCFolder::whereIn('FolderId', $ids)->update([
                'IsDeleted' => true,
                'LastUpdatedBy' => Auth::user()->UserID ?? 'System',
                'LastUpdatedOn' => now(),
            ]);

            Log::info('Document types bulk deleted successfully', ['count' => count($ids)]);

            return response()->json([
                'success' => true,
                'message' => count($ids) . ' document type(s) deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error bulk deleting document types', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting document types: ' . $e->getMessage(),
            ], 500);
        }
    }
}
