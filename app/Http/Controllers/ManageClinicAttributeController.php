<?php

namespace App\Http\Controllers;

use App\Models\ClinicAttributesMaster;
use App\Models\ClinicAttributesDatum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables as YajraDataTables;

class ManageClinicAttributeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $clinicId = Auth::user()->ClinicID ?? 'E403D9FF-A62D-463A-83D1-91C0EEEA2CD4';

            $query = ClinicAttributesMaster::leftJoin('ClinicAttributesData', function ($join) use ($clinicId) {
                    $join->on('ClinicAttributesMaster.AttributeName', '=', 'ClinicAttributesData.AttributeName')
                         ->where('ClinicAttributesData.ClinicID', '=', $clinicId)
                         ->where(function ($q) {
                             $q->where('ClinicAttributesData.IsDeleted', false)
                               ->orWhereNull('ClinicAttributesData.IsDeleted');
                         });
                })
                ->select([
                    'ClinicAttributesMaster.ClinicAttributeMasterID',
                    'ClinicAttributesMaster.AttributeName',
                    'ClinicAttributesMaster.AttributeDescription',
                    'ClinicAttributesData.ClinicAttributeDataID',
                    'ClinicAttributesData.AttributeValue',
                ]);

            return YajraDataTables::of($query)
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && $request->get('search') != '') {
                        $searchTerm = '%' . $request->get('search') . '%';
                        $query->where(function ($q) use ($searchTerm) {
                            $q->where('ClinicAttributesMaster.AttributeName', 'like', $searchTerm)
                              ->orWhere('ClinicAttributesMaster.AttributeDescription', 'like', $searchTerm)
                              ->orWhere('ClinicAttributesData.AttributeValue', 'like', $searchTerm);
                        });
                    }
                })
                ->editColumn('AttributeValue', function ($row) {
                    $value = $row->AttributeValue ?? '';
                    $dataId = $row->ClinicAttributeDataID ?? '';
                    $attrName = htmlspecialchars($row->AttributeName, ENT_QUOTES);
                    return '<input type="text" class="attr-value-input w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
                            value="' . htmlspecialchars($value, ENT_QUOTES) . '"
                            data-id="' . $dataId . '"
                            data-name="' . $attrName . '"
                            data-original="' . htmlspecialchars($value, ENT_QUOTES) . '">';
                })
                ->rawColumns(['AttributeValue'])
                ->make(true);
        }

        return view('admin.clinic-attributes.index');
    }

    public function updateValue(Request $request)
    {
        $request->validate([
            'AttributeName' => 'required|string',
            'AttributeValue' => 'nullable|string',
        ]);

        try {
            $clinicId = Auth::user()->ClinicID ?? 'E403D9FF-A62D-463A-83D1-91C0EEEA2CD4';
            $userId = Auth::user()->UserID ?? 'System';

            $existing = ClinicAttributesDatum::where('AttributeName', $request->AttributeName)
                ->where('ClinicID', $clinicId)
                ->where(function ($q) {
                    $q->where('IsDeleted', false)->orWhereNull('IsDeleted');
                })
                ->first();

            if ($existing) {
                $existing->update([
                    'AttributeValue' => $request->AttributeValue,
                    'LastUpdatedBy' => $userId,
                    'LastUpdatedOn' => now(),
                ]);
            } else {
                ClinicAttributesDatum::create([
                    'ClinicID' => $clinicId,
                    'AttributeName' => $request->AttributeName,
                    'AttributeValue' => $request->AttributeValue,
                    'IsDeleted' => false,
                    'CreatedBy' => $userId,
                    'CreatedOn' => now(),
                    'LastUpdatedBy' => $userId,
                    'LastUpdatedOn' => now(),
                    'rowguid' => strtoupper(Str::uuid()->toString()),
                ]);
            }

            Log::info('Clinic attribute updated', ['name' => $request->AttributeName]);

            return response()->json([
                'success' => true,
                'message' => 'Attribute value updated successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating clinic attribute', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating attribute: ' . $e->getMessage(),
            ], 500);
        }
    }
}
