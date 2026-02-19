<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ManageClinicController extends Controller
{
    public function index()
    {
        $clinic = Clinic::where('ClinicID', Auth::user()->ClinicID)->first();

        return view('admin.clinic.index', compact('clinic'));
    }

    public function update(Request $request)
    {
        $clinic = Clinic::where('ClinicID', Auth::user()->ClinicID)->firstOrFail();

        $request->validate([
            'Name' => 'required|string|max:255',
            'AuthenticationKey' => 'nullable|string|max:50',
            'Address1' => 'nullable|string',
            'Address2' => 'nullable|string',
            'CountryID' => 'nullable|integer',
            'State' => 'nullable|string|max:50',
            'City' => 'nullable|string|max:100',
            'Email' => 'nullable|email|max:50',
            'Phone' => 'nullable|string|max:50',
            'Fax' => 'nullable|string|max:50',
            'Description' => 'nullable|string|max:255',
            'FTPBackupServer' => 'nullable|string',
            'FTPUserID' => 'nullable|string|max:50',
            'FTPPassword' => 'nullable|string|max:50',
            'EmailHost' => 'nullable|string',
            'EmailPort' => 'nullable|string|max:50',
            'EmailUserid' => 'nullable|string',
            'EmailPassword' => 'nullable|string',
            'clinic_logo' => 'nullable|image|max:2048',
            'clinic_letterhead' => 'nullable|image|max:4096',
        ]);

        try {
            $updateData = [
                'Name' => $request->Name,
                'AuthenticationKey' => $request->AuthenticationKey,
                'Address1' => $request->Address1,
                'Address2' => $request->Address2,
                'CountryID' => $request->CountryID ?? 0,
                'State' => $request->State,
                'City' => $request->City,
                'Email' => $request->Email,
                'Phone' => $request->Phone,
                'Fax' => $request->Fax,
                'Description' => $request->Description,
                'FTPBackupServer' => $request->FTPBackupServer,
                'FTPUserID' => $request->FTPUserID,
                'FTPPassword' => $request->FTPPassword,
                'EmailHost' => $request->EmailHost,
                'EmailPort' => $request->EmailPort,
                'EmailUserid' => $request->EmailUserid,
                'EmailPassword' => $request->EmailPassword,
                'LastUpdatedBy' => Auth::user()->UserID ?? 'System',
                'LastUpdatedOn' => now(),
            ];

            // Handle Clinic Logo upload
            if ($request->hasFile('clinic_logo')) {
                $image = $request->file('clinic_logo');
                $imageContent = file_get_contents($image->getRealPath());
                $updateData['ClinicLogo'] = base64_encode($imageContent);
            }

            // Handle Letter Head Header upload
            if ($request->hasFile('clinic_letterhead')) {
                $image = $request->file('clinic_letterhead');
                $imageContent = file_get_contents($image->getRealPath());
                $updateData['ClinicLetterHeadHeader'] = base64_encode($imageContent);
            }

            $clinic->update($updateData);

            Log::info('Clinic updated successfully', ['clinic_id' => $clinic->ClinicID]);

            return response()->json([
                'success' => true,
                'message' => 'Clinic updated successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating clinic', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating clinic: ' . $e->getMessage(),
            ], 500);
        }
    }
}
