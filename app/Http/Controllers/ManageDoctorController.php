<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use App\Models\User;
use App\Models\UsersClinicInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables as YajraDataTables;

class ManageDoctorController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Provider::where('IsDeleted', false);

            return YajraDataTables::of($query)
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && $request->get('search') != '') {
                        $searchTerm = '%' . $request->get('search') . '%';
                        $query->where(function ($q) use ($searchTerm) {
                            $q->where('ProviderName', 'like', $searchTerm)
                              ->orWhere('Email', 'like', $searchTerm)
                              ->orWhere('PhoneNumber', 'like', $searchTerm)
                              ->orWhere('RegistrationNumber', 'like', $searchTerm);
                        });
                    }
                    if ($request->has('category') && $request->get('category') != '') {
                        $query->where('Category', $request->get('category'));
                    }
                })
                ->addColumn('action', function ($doctor) {
                    return view('admin.doctors.actions', compact('doctor'))->render();
                })
                ->editColumn('IncentiveType', function ($doctor) {
                    return $doctor->IncentiveType == 1 ? 'Fixed' : 'Percentage';
                })
                ->editColumn('IncentiveValue', function ($doctor) {
                    if ($doctor->IncentiveValue === null) return 'N/A';
                    return $doctor->IncentiveType == 2
                        ? $doctor->IncentiveValue . '%'
                        : number_format($doctor->IncentiveValue, 2);
                })
                ->editColumn('LastUpdatedOn', function ($doctor) {
                    return $doctor->LastUpdatedOn ? $doctor->LastUpdatedOn->format('M d, Y') : 'N/A';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.doctors.index');
    }

    public function create()
    {
        return view('admin.doctors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ProviderName' => 'required|string|max:100',
            'Email' => 'nullable|email|max:100',
            'PhoneNumber' => 'nullable|string|max:50',
            'Location' => 'nullable|string|max:50',
            'Experience' => 'nullable|string|max:1000',
            'Sequence' => 'nullable|integer',
            'RegistrationNumber' => 'nullable|string|max:1000',
            'Category' => 'nullable|string|max:50',
            'IncentiveType' => 'nullable|integer|in:1,2',
            'IncentiveValue' => 'nullable|numeric',
            'ColorCode' => 'nullable|string|max:100',
            'DisplayInAppointmentsView' => 'nullable|boolean',
            'start_time' => 'nullable|string',
            'end_time' => 'nullable|string',
            'slot_duration' => 'nullable|string',
            'provider_image' => 'nullable|image|max:2048',
        ]);

        try {
            $experienceYears = $request->experience_years ?? '0';
            $experienceMonths = $request->experience_months ?? '0';
            $experience = $experienceYears . ' years ' . $experienceMonths . ' months';

            $providerData = [
                'ProviderName' => $request->ProviderName,
                'Email' => $request->Email,
                'PhoneNumber' => $request->PhoneNumber,
                'Location' => $request->Location,
                'Experience' => $experience,
                'Sequence' => $request->Sequence ?? 0,
                'RegistrationNumber' => $request->RegistrationNumber,
                'Category' => $request->Category,
                'IncentiveType' => $request->IncentiveType ?? 1,
                'IncentiveValue' => $request->IncentiveValue,
                'ColorCode' => $request->ColorCode,
                'DisplayInAppointmentsView' => $request->has('DisplayInAppointmentsView') ? true : true,
                'ClinicID' => Auth::user()->ClinicID,
                'IsDeleted' => false,
                'LastUpdatedBy' => Auth::user()->UserID ?? 'System',
                'LastUpdatedOn' => now(),
                'rowguid' => strtoupper(Str::uuid()->toString()),
                'Attribute1' => $request->start_time,
                'Attribute2' => $request->end_time,
                'Attribute3' => $request->slot_duration,
            ];

            // Handle image upload
            if ($request->hasFile('provider_image')) {
                $imageContent = file_get_contents($request->file('provider_image')->getRealPath());
                $providerData['ProviderImage'] = base64_encode($imageContent);
            }

            // If user credentials provided, create user and link
            if ($request->filled('login_id') && $request->filled('login_password')) {
                $request->validate([
                    'login_id' => 'required|email|unique:Users,Email',
                    'login_password' => 'required|string|min:6',
                ]);

                $doctorRole = Role::where('name', 'like', '%doctor%')->first();

                $user = User::create([
                    'Name' => $request->ProviderName,
                    'Password' => Hash::make($request->login_password),
                    'Email' => $request->login_id,
                    'UserName' => $request->login_id,
                    'ClientID' => 'E403D9FF-A62D-463A-83D1-91C0EEEA2CD4',
                    'RoleID' => $doctorRole ? $doctorRole->RoleID : null,
                    'CreatedOn' => now(),
                    'CreatedBy' => Auth::user()->UserID ?? 'System',
                    'LastUpdatedOn' => now(),
                    'LastUpdatedBy' => Auth::user()->UserID ?? 'System',
                    'SecurityQuestion' => 'pet',
                    'SecurityAnswer' => 'default',
                ]);

                if ($doctorRole) {
                    $user->assignRole($doctorRole->name);
                }

                $providerData['UserID'] = $user->UserID;
            }

            $provider = Provider::create($providerData);

            // Create UsersClinicInfo if user was created
            if (isset($user) && $provider) {
                UsersClinicInfo::create([
                    'UserID' => $user->UserID,
                    'ClinicID' => Auth::user()->ClinicID,
                    'ProviderID' => $provider->ProviderID,
                    'IsDeleted' => false,
                    'CreatedOn' => now(),
                    'CreatedBy' => Auth::user()->UserID ?? 'System',
                    'LastUpdatedOn' => now(),
                    'LastUpdatedBy' => Auth::user()->UserID ?? 'System',
                ]);
            }

            Log::info('Doctor created successfully', ['provider_id' => $provider->ProviderID]);

            return response()->json([
                'success' => true,
                'message' => 'Doctor created successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating doctor', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error creating doctor: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function edit($id)
    {
        $doctor = Provider::findOrFail($id);
        return view('admin.doctors.edit', compact('doctor'));
    }

    public function update(Request $request, $id)
    {
        $doctor = Provider::findOrFail($id);

        $request->validate([
            'ProviderName' => 'required|string|max:100',
            'Email' => 'nullable|email|max:100',
            'PhoneNumber' => 'nullable|string|max:50',
            'Location' => 'nullable|string|max:50',
            'Sequence' => 'nullable|integer',
            'RegistrationNumber' => 'nullable|string|max:1000',
            'Category' => 'nullable|string|max:50',
            'IncentiveType' => 'nullable|integer|in:1,2',
            'IncentiveValue' => 'nullable|numeric',
            'ColorCode' => 'nullable|string|max:100',
            'start_time' => 'nullable|string',
            'end_time' => 'nullable|string',
            'slot_duration' => 'nullable|string',
            'provider_image' => 'nullable|image|max:2048',
        ]);

        try {
            $experienceYears = $request->experience_years ?? '0';
            $experienceMonths = $request->experience_months ?? '0';
            $experience = $experienceYears . ' years ' . $experienceMonths . ' months';

            $updateData = [
                'ProviderName' => $request->ProviderName,
                'Email' => $request->Email,
                'PhoneNumber' => $request->PhoneNumber,
                'Location' => $request->Location,
                'Experience' => $experience,
                'Sequence' => $request->Sequence ?? 0,
                'RegistrationNumber' => $request->RegistrationNumber,
                'Category' => $request->Category,
                'IncentiveType' => $request->IncentiveType ?? 1,
                'IncentiveValue' => $request->IncentiveValue,
                'ColorCode' => $request->ColorCode,
                'DisplayInAppointmentsView' => $request->has('DisplayInAppointmentsView') ? true : $doctor->DisplayInAppointmentsView,
                'LastUpdatedBy' => Auth::user()->UserID ?? 'System',
                'LastUpdatedOn' => now(),
                'Attribute1' => $request->start_time,
                'Attribute2' => $request->end_time,
                'Attribute3' => $request->slot_duration,
            ];

            // Handle image upload
            if ($request->hasFile('provider_image')) {
                $imageContent = file_get_contents($request->file('provider_image')->getRealPath());
                $updateData['ProviderImage'] = base64_encode($imageContent);
            }

            $doctor->update($updateData);

            // Update linked user if exists
            if ($doctor->UserID) {
                $user = User::where('UserID', $doctor->UserID)->first();
                if ($user) {
                    $user->update([
                        'Name' => $request->ProviderName,
                        'Email' => $request->Email ?? $user->Email,
                        'LastUpdatedOn' => now(),
                        'LastUpdatedBy' => Auth::user()->UserID ?? 'System',
                    ]);
                }
            }

            Log::info('Doctor updated successfully', ['provider_id' => $doctor->ProviderID]);

            return response()->json([
                'success' => true,
                'message' => 'Doctor updated successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating doctor', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating doctor: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $doctor = Provider::findOrFail($id);
            $doctor->update([
                'IsDeleted' => true,
                'DisplayInAppointmentsView' => false,
                'LastUpdatedBy' => Auth::user()->UserID ?? 'System',
                'LastUpdatedOn' => now(),
            ]);

            // Also soft-delete linked UsersClinicInfo
            if ($doctor->UserID) {
                UsersClinicInfo::where('ProviderID', $doctor->ProviderID)
                    ->update([
                        'IsDeleted' => true,
                        'LastUpdatedOn' => now(),
                        'LastUpdatedBy' => Auth::user()->UserID ?? 'System',
                    ]);
            }

            Log::info('Doctor deleted successfully', ['provider_id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Doctor deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting doctor', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting doctor: ' . $e->getMessage(),
            ], 500);
        }
    }
}
