<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class ManageClinicController extends Controller
{
    public function index()
    {
        return view('admin.clinic.index');
    }

    public function getClinicsData(Request $request)
    {
        $clinics = Clinic::query();

        return DataTables::of($clinics)
            ->addColumn('serial', function($clinic) {
                return '';
            })
            ->addColumn('actions', function($clinic) {
                return '
                    <div class="flex gap-2">
                        <a href="' . route('admin.clinic.edit', $clinic->ClinicID) . '" 
                           class="text-blue-600 hover:text-blue-800 transition-colors" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>
                        <button onclick="deleteClinic(\'' . $clinic->ClinicID . '\')" 
                                class="text-red-600 hover:text-red-800 transition-colors" title="Delete">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                ';
            })
            ->addColumn('Country', function($clinic) {
                switch($clinic->CountryID) {
                    case 1: return 'India';
                    case 2: return 'United States';
                    case 3: return 'United Kingdom';
                    case 4: return 'Canada';
                    case 5: return 'Australia';
                    case 6: return 'UAE';
                    case 7: return 'Singapore';
                    case 8: return 'Malaysia';
                    default: return '-';
                }
            })
            ->editColumn('CreatedOn', function($clinic) {
                return $clinic->CreatedOn ? \Carbon\Carbon::parse($clinic->CreatedOn)->format('M d, Y') : '-';
            })
            ->editColumn('Email', function($clinic) {
                return $clinic->Email ?? '-';
            })
            ->editColumn('Phone', function($clinic) {
                return $clinic->Phone ?? '-';
            })
            ->editColumn('City', function($clinic) {
                return $clinic->City ?? '-';
            })
            ->editColumn('State', function($clinic) {
                return $clinic->State ?? '-';
            })
            ->rawColumns(['actions', 'serial'])
            ->make(true);
    }

    public function create()
    {
        $countries = \App\Models\Country::orderBy('CountryName')->get();
        $states = \App\Models\State::orderBy('StateDesc')->get();
        return view('admin.clinic.create', compact('countries', 'states'));
    }

    public function getStatesByCountry($countryId)
    {
        $states = \App\Models\State::where('CountryID', $countryId)->orderBy('StateDesc')->get();
        return response()->json($states);
    }

    public function store(Request $request)
    {
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
            $createData = [
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
                'CreatedOn' => now(),
                'CreatedBy' => Auth::user()->UserID ?? 'System',
                'LastUpdatedBy' => Auth::user()->UserID ?? 'System',
                'LastUpdatedOn' => now(),
            ];

            // Handle Clinic Logo upload
            if ($request->hasFile('clinic_logo')) {
                $image = $request->file('clinic_logo');
                $imageContent = file_get_contents($image->getRealPath());
                $createData['ClinicLogo'] = base64_encode($imageContent);
            }

            // Handle Letter Head Header upload
            if ($request->hasFile('clinic_letterhead')) {
                $image = $request->file('clinic_letterhead');
                $imageContent = file_get_contents($image->getRealPath());
                $createData['ClinicLetterHeadHeader'] = base64_encode($imageContent);
            }

            $clinic = Clinic::create($createData);

            Log::info('Clinic created successfully', ['clinic_id' => $clinic->ClinicID]);

            return response()->json([
                'success' => true,
                'message' => 'Clinic created successfully',
                'clinic' => $clinic
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating clinic', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error creating clinic: ' . $e->getMessage(),
            ], 500);
        }
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

    public function edit($id)
    {
        $clinic = Clinic::findOrFail($id);
        $countries = Country::orderBy('CountryName')->get();
        $states = State::orderBy('StateDesc')->get();
        return view('admin.clinic.edit', compact('clinic', 'countries', 'states'));
    }

    public function destroy($id)
    {
        try {
            $clinic = Clinic::findOrFail($id);
            $clinic->delete();

            Log::info('Clinic deleted successfully', ['clinic_id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Clinic deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting clinic', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting clinic: ' . $e->getMessage()
            ], 500);
        }
    }
}
