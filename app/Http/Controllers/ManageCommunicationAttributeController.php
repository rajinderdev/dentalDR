<?php

namespace App\Http\Controllers;

use App\Models\ClinicCommunicationMaster;
use App\Models\ClinicCommunicationConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ManageCommunicationAttributeController extends Controller
{
    public function index()
    {
        $clinicId = Auth::user()->ClinicID ?? 'E403D9FF-A62D-463A-83D1-91C0EEEA2CD4';

        // Get all master records with their clinic-specific configs
        $masters = ClinicCommunicationMaster::leftJoin('ClinicCommunicationConfig', function ($join) use ($clinicId) {
                $join->on('ClinicCommunicationMaster.ClinicCommunicationMasterID', '=', 'ClinicCommunicationConfig.ClinicCommunicationMasterID')
                     ->where('ClinicCommunicationConfig.ClinicID', '=', $clinicId);
            })
            ->select([
                'ClinicCommunicationMaster.*',
                'ClinicCommunicationConfig.ClinicCommunicationConfigID',
                'ClinicCommunicationConfig.AttributeValue1',
                'ClinicCommunicationConfig.AttributeValue2',
                'ClinicCommunicationConfig.IsActive',
            ])
            ->orderBy('ClinicCommunicationMaster.CommunicationMasterTypeID')
            ->orderBy('ClinicCommunicationMaster.Category')
            ->orderBy('ClinicCommunicationMaster.CommunicationMasterSubTypeID')
            ->get();

        // Group: TypeID 1 = SMS, TypeID 2 = Email
        $smsItems = $masters->where('CommunicationMasterTypeID', 1);
        $emailItems = $masters->where('CommunicationMasterTypeID', 2);

        // Sub-group by execution type: 1 = Automatic, 2 = Manual
        $smsAutomatic = $smsItems->where('CommunicationExecutionType', 1)->groupBy('Category');
        $smsManual = $smsItems->where('CommunicationExecutionType', 2)->groupBy('Category');
        $emailAutomatic = $emailItems->where('CommunicationExecutionType', 1)->groupBy('Category');
        $emailManual = $emailItems->where('CommunicationExecutionType', 2)->groupBy('Category');

        return view('admin.communication-attributes.index', compact(
            'smsAutomatic', 'smsManual', 'emailAutomatic', 'emailManual'
        ));
    }

    public function update(Request $request)
    {
        try {
            $clinicId = Auth::user()->ClinicID ?? 'E403D9FF-A62D-463A-83D1-91C0EEEA2CD4';
            $userId = Auth::user()->UserID ?? 'System';
            $items = $request->input('items', []);

            foreach ($items as $masterId => $data) {
                $config = ClinicCommunicationConfig::where('ClinicCommunicationMasterID', $masterId)
                    ->where('ClinicID', $clinicId)
                    ->first();

                if ($config) {
                    $config->update([
                        'AttributeValue1' => $data['value1'] ?? null,
                        'AttributeValue2' => $data['value2'] ?? null,
                        'IsActive' => isset($data['active']) && $data['active'] == '1',
                        'LastUpdatedBy' => $userId,
                        'LastUpdatedOn' => now(),
                    ]);
                } else {
                    ClinicCommunicationConfig::create([
                        'ClinicID' => $clinicId,
                        'ClinicCommunicationMasterID' => $masterId,
                        'AttributeValue1' => $data['value1'] ?? null,
                        'AttributeValue2' => $data['value2'] ?? null,
                        'IsActive' => isset($data['active']) && $data['active'] == '1',
                        'LastUpdatedBy' => $userId,
                        'LastUpdatedOn' => now(),
                    ]);
                }
            }

            Log::info('Communication attributes updated', ['count' => count($items)]);

            return response()->json([
                'success' => true,
                'message' => 'Communication attributes updated successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating communication attributes', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating attributes: ' . $e->getMessage(),
            ], 500);
        }
    }
}
