<?php

namespace App\Http\Controllers;

use App\Models\ClinicChair;
use App\Models\ChairSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables as YajraDataTables;

class ManageChairController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ClinicChair::where('IsDeleted', false)
                ->where('ClinicID', Auth::user()->ClinicID);

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
                ->addColumn('StartTime', function ($chair) {
                    $slot = $chair->chair_slots()->first();
                    if ($slot && $slot->StartDatetime) {
                        return $slot->StartDatetime->format('h:i A');
                    }
                    return 'N/A';
                })
                ->addColumn('EndTime', function ($chair) {
                    $slot = $chair->chair_slots()->first();
                    if ($slot && $slot->EndDateTime) {
                        return $slot->EndDateTime->format('h:i A');
                    }
                    return 'N/A';
                })
                ->addColumn('TimeInterval', function ($chair) {
                    $slot = $chair->chair_slots()->first();
                    if ($slot && $slot->SlotInterval) {
                        return $slot->SlotInterval . ' min';
                    }
                    return 'N/A';
                })
                ->addColumn('action', function ($chair) {
                    return view('admin.chairs.actions', compact('chair'))->render();
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.chairs.index');
    }

    public function create()
    {
        return view('admin.chairs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'Title' => 'required|string|max:255',
            'Description' => 'nullable|string',
            'start_time' => 'nullable|string',
            'end_time' => 'nullable|string',
            'slot_interval' => 'nullable|integer|min:1',
        ]);

        try {
            $chair = ClinicChair::create([
                'ClinicID' => Auth::user()->ClinicID,
                'Title' => $request->Title,
                'Description' => $request->Description,
                'IsDeleted' => false,
                'CreatedOn' => now(),
                'CreatedBy' => Auth::user()->UserID ?? 'System',
                'LastUpdatedOn' => now(),
                'LastUpdatedBy' => Auth::user()->UserID ?? 'System',
            ]);

            // Create chair slot if timing provided
            if ($request->filled('start_time') || $request->filled('end_time') || $request->filled('slot_interval')) {
                $startTime = $request->start_time ? now()->setTimeFromTimeString($request->start_time) : null;
                $endTime = $request->end_time ? now()->setTimeFromTimeString($request->end_time) : null;

                ChairSlot::create([
                    'ChairID' => $chair->ChairID,
                    'StartDatetime' => $startTime,
                    'EndDateTime' => $endTime,
                    'SlotInterval' => $request->slot_interval,
                    'CreatedOn' => now(),
                    'CreatedBy' => Auth::user()->UserID ?? 'System',
                    'LastUpdatedOn' => now(),
                    'LastUpdatedBy' => Auth::user()->UserID ?? 'System',
                ]);
            }

            Log::info('Chair created successfully', ['chair_id' => $chair->ChairID]);

            return response()->json([
                'success' => true,
                'message' => 'Chair created successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating chair', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error creating chair: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function edit($id)
    {
        $chair = ClinicChair::findOrFail($id);
        $chairSlot = $chair->chair_slots()->first();
        return view('admin.chairs.edit', compact('chair', 'chairSlot'));
    }

    public function update(Request $request, $id)
    {
        $chair = ClinicChair::findOrFail($id);

        $request->validate([
            'Title' => 'required|string|max:255',
            'Description' => 'nullable|string',
            'start_time' => 'nullable|string',
            'end_time' => 'nullable|string',
            'slot_interval' => 'nullable|integer|min:1',
        ]);

        try {
            $chair->update([
                'Title' => $request->Title,
                'Description' => $request->Description,
                'LastUpdatedOn' => now(),
                'LastUpdatedBy' => Auth::user()->UserID ?? 'System',
            ]);

            // Update or create chair slot
            $startTime = $request->start_time ? now()->setTimeFromTimeString($request->start_time) : null;
            $endTime = $request->end_time ? now()->setTimeFromTimeString($request->end_time) : null;

            $existingSlot = $chair->chair_slots()->first();
            if ($existingSlot) {
                $existingSlot->update([
                    'StartDatetime' => $startTime,
                    'EndDateTime' => $endTime,
                    'SlotInterval' => $request->slot_interval,
                    'LastUpdatedOn' => now(),
                    'LastUpdatedBy' => Auth::user()->UserID ?? 'System',
                ]);
            } else {
                ChairSlot::create([
                    'ChairID' => $chair->ChairID,
                    'StartDatetime' => $startTime,
                    'EndDateTime' => $endTime,
                    'SlotInterval' => $request->slot_interval,
                    'CreatedOn' => now(),
                    'CreatedBy' => Auth::user()->UserID ?? 'System',
                    'LastUpdatedOn' => now(),
                    'LastUpdatedBy' => Auth::user()->UserID ?? 'System',
                ]);
            }

            Log::info('Chair updated successfully', ['chair_id' => $chair->ChairID]);

            return response()->json([
                'success' => true,
                'message' => 'Chair updated successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating chair', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating chair: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $chair = ClinicChair::findOrFail($id);
            $chair->update([
                'IsDeleted' => true,
                'LastUpdatedBy' => Auth::user()->UserID ?? 'System',
                'LastUpdatedOn' => now(),
            ]);

            Log::info('Chair deleted successfully', ['chair_id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Chair deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting chair', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting chair: ' . $e->getMessage(),
            ], 500);
        }
    }
}
