<?php

namespace App\Http\Controllers;

use App\Models\ClinicLabWorkComponent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables as YajraDataTables;

class ManageLabItemController extends Controller
{
    /**
     * Get the list of component categories.
     */
    private function getCategories()
    {
        // Get distinct categories from existing data
        $dbCategories = ClinicLabWorkComponent::where('IsDeleted', false)
            ->whereNotNull('ComponentCategoryID')
            ->where('ComponentCategoryID', '!=', '')
            ->distinct()
            ->pluck('ComponentCategoryID')
            ->toArray();

        // Default dental lab categories
        $defaultCategories = [
            'IMPLANT CROWN/BRIDGE',
            'TMJ APPLIANCES',
            'ORTHO',
            'CROWNS',
            'BRIDGE',
            'DENTURES',
            'ONLAY/INLAY',
        ];

        return collect(array_unique(array_merge($defaultCategories, $dbCategories)))
            ->sort()
            ->values();
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ClinicLabWorkComponent::where('IsDeleted', false)
                ->where('ClinicID', Auth::user()->ClinicID);

            return YajraDataTables::of($query)
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && $request->get('search') != '') {
                        $searchTerm = '%' . $request->get('search') . '%';
                        $query->where(function ($q) use ($searchTerm) {
                            $q->where('ComponentName', 'like', $searchTerm)
                              ->orWhere('ComponentDescription', 'like', $searchTerm)
                              ->orWhere('ComponentCategoryID', 'like', $searchTerm);
                        });
                    }
                    if ($request->has('category') && $request->get('category') != '') {
                        $query->where('ComponentCategoryID', $request->get('category'));
                    }
                })
                ->addColumn('action', function ($item) {
                    return view('admin.lab-items.actions', compact('item'))->render();
                })
                ->editColumn('LabWorkCost', function ($item) {
                    if ($item->LabWorkCost === null) return 'INR 0.00';
                    return 'INR ' . number_format($item->LabWorkCost, 2);
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $categories = $this->getCategories();
        return view('admin.lab-items.index', compact('categories'));
    }

    public function create(Request $request)
    {
        $categories = $this->getCategories();
        $selectedCategory = $request->get('category', '');
        return view('admin.lab-items.create', compact('categories', 'selectedCategory'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ComponentCategoryID' => 'required|string|max:255',
            'ComponentName' => 'required|string|max:255',
            'ComponentDescription' => 'nullable|string',
            'LabWorkCost' => 'nullable|numeric|min:0',
        ]);

        try {
            ClinicLabWorkComponent::create([
                'ClinicID' => Auth::user()->ClinicID,
                'ComponentCategoryID' => $request->ComponentCategoryID,
                'ComponentName' => $request->ComponentName,
                'ComponentDescription' => $request->ComponentDescription,
                'LabWorkCost' => $request->LabWorkCost ?? 0,
                'IsDeleted' => false,
                'CreatedOn' => now(),
                'CreatedBy' => Auth::user()->UserID ?? 'System',
                'LastUpdatedOn' => now(),
                'LastUpdatedBy' => Auth::user()->UserID ?? 'System',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lab item created successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating lab item', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error creating lab item: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function edit($id)
    {
        $item = ClinicLabWorkComponent::findOrFail($id);
        $categories = $this->getCategories();
        return view('admin.lab-items.edit', compact('item', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $item = ClinicLabWorkComponent::findOrFail($id);

        $request->validate([
            'ComponentCategoryID' => 'required|string|max:255',
            'ComponentName' => 'required|string|max:255',
            'ComponentDescription' => 'nullable|string',
            'LabWorkCost' => 'nullable|numeric|min:0',
        ]);

        try {
            $item->update([
                'ComponentCategoryID' => $request->ComponentCategoryID,
                'ComponentName' => $request->ComponentName,
                'ComponentDescription' => $request->ComponentDescription,
                'LabWorkCost' => $request->LabWorkCost ?? 0,
                'LastUpdatedOn' => now(),
                'LastUpdatedBy' => Auth::user()->UserID ?? 'System',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lab item updated successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating lab item', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating lab item: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $item = ClinicLabWorkComponent::findOrFail($id);
            $item->update([
                'IsDeleted' => true,
                'LastUpdatedBy' => Auth::user()->UserID ?? 'System',
                'LastUpdatedOn' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lab item deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting lab item', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting lab item: ' . $e->getMessage(),
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
            ClinicLabWorkComponent::whereIn('LabWorkComponentID', $request->ids)->update([
                'IsDeleted' => true,
                'LastUpdatedBy' => Auth::user()->UserID ?? 'System',
                'LastUpdatedOn' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => count($request->ids) . ' item(s) deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error bulk deleting lab items', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting items: ' . $e->getMessage(),
            ], 500);
        }
    }
}
