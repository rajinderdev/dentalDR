<?php

namespace App\Http\Controllers;

use App\Models\LookUp;
use App\Models\LookUpsMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables as YajraDataTables;

class ManageLookUpController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = LookUp::where('IsDeleted', false);

            return YajraDataTables::of($query)
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && $request->get('search') != '') {
                        $searchTerm = '%' . $request->get('search') . '%';
                        $query->where(function ($q) use ($searchTerm) {
                            $q->where('ItemTitle', 'like', $searchTerm)
                              ->orWhere('ItemDescription', 'like', $searchTerm);
                        });
                    }
                    if ($request->has('category') && $request->get('category') != '') {
                        $query->where('ItemCategory', $request->get('category'));
                    }
                })
                ->addColumn('action', function ($lookup) {
                    return view('admin.lookups.actions', compact('lookup'))->render();
                })
                ->editColumn('LastUpdatedOn', function ($lookup) {
                    return $lookup->LastUpdatedOn ? $lookup->LastUpdatedOn->format('M d, Y') : 'N/A';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $categories = LookUpsMaster::where('IsDeleted', false)
            ->orderBy('ItemCategory')
            ->pluck('ItemCategory')
            ->unique()
            ->values();

        return view('admin.lookups.index', compact('categories'));
    }

    public function create(Request $request)
    {
        $categories = LookUpsMaster::where('IsDeleted', false)
            ->orderBy('ItemCategory')
            ->pluck('ItemCategory')
            ->unique()
            ->values();

        $selectedCategory = $request->get('category', '');

        return view('admin.lookups.create', compact('categories', 'selectedCategory'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ItemTitle' => 'required|string|max:255',
            'ItemDescription' => 'nullable|string|max:255',
            'ItemCategory' => 'required|string|max:50',
            'Importance' => 'nullable|integer|min:0',
        ]);

        try {
            // Get next ItemID for this category
            $maxItemId = LookUp::where('ItemCategory', $request->ItemCategory)->max('ItemID');
            $nextItemId = ($maxItemId ?? 0) + 1;

            LookUp::create([
                'ClinicID' => Auth::user()->ClinicID,
                'ItemID' => $nextItemId,
                'ItemTitle' => $request->ItemTitle,
                'ItemDescription' => $request->ItemDescription,
                'ItemCategory' => $request->ItemCategory,
                'IsDeleted' => false,
                'Importance' => $request->Importance ?? 0,
                'LastUpdatedBy' => Auth::user()->UserID ?? 'System',
                'LastUpdatedOn' => now(),
                'rowguid' => strtoupper(Str::uuid()->toString()),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lookup item created successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating lookup item', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error creating lookup item: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function edit($id)
    {
        $lookup = LookUp::findOrFail($id);

        $categories = LookUpsMaster::where('IsDeleted', false)
            ->orderBy('ItemCategory')
            ->pluck('ItemCategory')
            ->unique()
            ->values();

        return view('admin.lookups.edit', compact('lookup', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $lookup = LookUp::findOrFail($id);

        $request->validate([
            'ItemTitle' => 'required|string|max:255',
            'ItemDescription' => 'nullable|string|max:255',
            'ItemCategory' => 'required|string|max:50',
            'Importance' => 'nullable|integer|min:0',
        ]);

        try {
            $lookup->update([
                'ItemTitle' => $request->ItemTitle,
                'ItemDescription' => $request->ItemDescription,
                'ItemCategory' => $request->ItemCategory,
                'Importance' => $request->Importance ?? 0,
                'LastUpdatedBy' => Auth::user()->UserID ?? 'System',
                'LastUpdatedOn' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lookup item updated successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating lookup item', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating lookup item: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $lookup = LookUp::findOrFail($id);
            $lookup->update([
                'IsDeleted' => true,
                'LastUpdatedBy' => Auth::user()->UserID ?? 'System',
                'LastUpdatedOn' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lookup item deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting lookup item', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting lookup item: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'string',
        ]);

        try {
            LookUp::whereIn('id', $request->ids)->update([
                'IsDeleted' => true,
                'LastUpdatedBy' => Auth::user()->UserID ?? 'System',
                'LastUpdatedOn' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => count($request->ids) . ' item(s) deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error bulk deleting lookup items', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting items: ' . $e->getMessage(),
            ], 500);
        }
    }
}
