<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Provider;
use App\Models\UsersClinicInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables as YajraDataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Helpers\EntityDataHelper;
class ManageUserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = User::where('IsDeleted', 0)->with(['role']);

            return YajraDataTables::of($query)
            ->filter(function ($query) use ($request) {
                if ($request->has('search') && $request->get('search') != '') {
                    $query->where('UserName', 'like', '%' . $request->get('search') . '%')
                          ->orWhere('Name', 'like', '%' . $request->get('search') . '%')
                          ->orWhere('Email', 'like', '%' . $request->get('search') . '%');
                }
                if ($request->has('user_type') && $request->get('user_type') != '') {
                    $query->whereHas('role', function($q) use ($request) {
                        $q->where('RoleID', $request->get('user_type'));
                    });
                }
                if ($request->has('status') && $request->get('status') != '') {
                    if($request->get('status') == 'approved') {
                        $query->where('Approved', 1);
                    } else if($request->get('status') == 'unapproved') {
                        $query->where('Approved', 0);
                    }
                    elseif($request->get('status') == 'locked') {
                        $query->where('Locked', 1);
                    }
                    elseif($request->get('status') == 'unlocked') {
                        $query->where('Locked', 0);
                    }
                }
            })
                ->addColumn('action', function($user) {
                    return view('admin.users.actions', compact('user'))->render();
                })
                ->editColumn('id', function($user) {
                    return $user->UserID;
                })
                ->editColumn('UserType', function($user) {
                    $role = Role::where('RoleID', $user->RoleID)->first();
                    return $role ? $role->name : 'N/A';
                })
                ->editColumn('status', function($user) {
                    $statusClasses = [
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'processing' => 'bg-blue-100 text-blue-800',
                        'completed' => 'bg-green-100 text-green-800',
                        'cancelled' => 'bg-red-100 text-red-800',
                    ];
                    
                    // For users, we'll show static status since status field doesn't exist
                    return '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>';
                })
                ->editColumn('CreatedOn', function($user) {
                    return $user->CreatedOn ? $user->CreatedOn->format('M d, Y h:i A') : 'N/A';
                })
                ->addColumn('customer', function($user) {
                    return $user->Name ?? 'N/A';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        $roles = Role::all();
        return view('admin.users.index', compact('roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
            'email' => 'required|email|max:255|unique:Users,email',
            'security_answer' => 'required|string|max:255',
            'security_question' => 'required|string|max:255',
            'RoleID' => 'required|string|exists:aspnet_Roles,RoleId'
        ]);

        try {
            $user = User::create([
                'Name' => $request->name,
                'Password' => Hash::make($request->password),
                'Email' => $request->email,
                'ClientID' => 'E403D9FF-A62D-463A-83D1-91C0EEEA2CD4',
                'UserName' => $request->email,
                'RoleID' => $request->RoleID,
                'CreatedOn' => now(),
                'CreatedBy' => Auth::user()->UserID ?? 'System',
                'LastUpdatedOn' => now(),
                'LastUpdatedBy' => Auth::user()->UserID ?? 'System',
                'SecurityQuestion' => $request->security_question,
                'SecurityAnswer' => $request->security_answer
            ]);

            // Assign role to user
            $role = Role::where('RoleID', $request->RoleID)->first();
            if ($role) {
                $user->assignRole($role->name);
                // Create provider if role is doctor
                if (strtolower($role->name) == 'doctor') {
                    $provider = Provider::create([
                        'ProviderName' => $request->name,
                        'Email' => $request->email,
                        'UserID' => $user->UserID,
                        'ClinicID' => EntityDataHelper::getClinicId(),
                        'CreatedOn' => now(),
                        'CreatedBy' => Auth::user()->UserID ?? 'System',
                        'LastUpdatedOn' => now(),
                        'LastUpdatedBy' => Auth::user()->UserID ?? 'System',
                        'IsDeleted' => false,
                        'DisplayInAppointmentsView' => true,
                        'rowguid' => strtoupper(Str::uuid()->toString())
                    ]);
                    if($provider){
                        // Create UsersClinicInfo record
                        UsersClinicInfo::create([
                            'UserID' => $user->UserID,
                            'ClinicID' => EntityDataHelper::getClinicId(),
                            'ProviderID' => $provider->ProviderID,
                            'IsDeleted' => false,
                            'CreatedOn' => now(),
                            'CreatedBy' => Auth::user()->UserID ?? 'System',
                            'LastUpdatedOn' => now(),
                            'LastUpdatedBy' => Auth::user()->UserID ?? 'System'
                        ]);
                    }
                }
            }
            
            Log::info('User created successfully', ['user_id' => $user->UserID]);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'user' => $user
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating user', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error creating user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view('admin.manage-account', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'security_answer' => 'required|string|max:255',
            'approved' => 'required|boolean',
            'locked' => 'required|boolean',
            'security_question' => 'required|string|max:255',
            'user_type' => 'required|string'
        ]);
        // Password validation only if provided
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:8|confirmed'
            ]);
        }

        try {
            $updateData = [
                'Name' => $request->name,
                'Email' => $request->email,
                'SecurityQuestion' => $request->security_question,
                'Approved' => $request->approved,
                'Locked' => $request->locked,
                'SecurityAnswer' => $request->security_answer,
                'RoleID' => $request->user_type,
                'Mobile'=>$request->mobile,
                'LastUpdatedOn' => now(),
                'LastUpdatedBy' => Auth::user()->Name ?? 'System',
            ];

            // Update password only if provided
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            // Handle provider creation/update for doctor role
            $role = Role::find($request->user_type);
            if ($role && strtolower($role->name) === 'doctor') {
                $existingProvider = Provider::where('UserID', $user->UserID)->first();
                
                $providerData = [
                    'ProviderName' => $request->name,
                    'Email' => $request->email,
                    'UserID' => $user->UserID,
                    'ClinicID' => Auth::user()->ClinicID ?? 'E403D9FF-A62D-463A-83D1-91C0EEEA2CD4',
                    'LastUpdatedOn' => now(),
                    'LastUpdatedBy' => Auth::user()->UserID ?? 'System',
                    'IsDeleted' => false,
                    'DisplayInAppointmentsView' => true
                ];

                if ($existingProvider) {
                    // Update existing provider
                    $existingProvider->update($providerData);
                    Log::info('Provider updated successfully', ['provider_id' => $existingProvider->ProviderID, 'user_id' => $user->UserID]);
                    
                    // Update UsersClinicInfo
                    $existingClinicInfo = UsersClinicInfo::where('UserID', $user->UserID)->first();
                    if ($existingClinicInfo) {
                        $existingClinicInfo->update([
                            'ProviderID' => $existingProvider->ProviderID,
                            'LastUpdatedOn' => now(),
                            'LastUpdatedBy' => Auth::user()->UserID ?? 'System'
                        ]);
                    } else {
                        UsersClinicInfo::create([
                            'UserID' => $user->UserID,
                            'ClinicID' => Auth::user()->ClinicID ?? 'E403D9FF-A62D-463A-83D1-91C0EEEA2CD4',
                            'ProviderID' => $existingProvider->ProviderID,
                            'IsDeleted' => false,
                            'CreatedOn' => now(),
                            'CreatedBy' => Auth::user()->UserID ?? 'System',
                            'LastUpdatedOn' => now(),
                            'LastUpdatedBy' => Auth::user()->UserID ?? 'System'
                        ]);
                    }
                } else {
                    // Create new provider
                    $providerData['CreatedOn'] = now();
                    $providerData['CreatedBy'] = Auth::user()->UserID ?? 'System';
                    $providerData['rowguid'] = strtoupper(Str::uuid()->toString());
                    $provider = Provider::create($providerData);
                    Log::info('Provider created successfully', ['user_id' => $user->UserID]);
                    
                    // Create UsersClinicInfo for new provider
                    UsersClinicInfo::create([
                        'UserID' => $user->UserID,
                        'ClinicID' => Auth::user()->ClinicID ?? 'E403D9FF-A62D-463A-83D1-91C0EEEA2CD4',
                        'ProviderID' => $provider->ProviderID,
                        'IsDeleted' => false,
                        'CreatedOn' => now(),
                        'CreatedBy' => Auth::user()->UserID ?? 'System',
                        'LastUpdatedOn' => now(),
                        'LastUpdatedBy' => Auth::user()->UserID ?? 'System'
                    ]);
                }
            } else {
                // If role is not doctor, check if provider exists and delete/disable it
                $existingProvider = Provider::where('UserID', $user->UserID)->first();
                if ($existingProvider) {
                    $existingProvider->update([
                        'IsDeleted' => true,
                        'DisplayInAppointmentsView' => false,
                        'LastUpdatedOn' => now(),
                        'LastUpdatedBy' => Auth::user()->UserID ?? 'System'
                    ]);
                    Log::info('Provider disabled for non-doctor role', ['provider_id' => $existingProvider->ProviderID, 'user_id' => $user->UserID]);
                    
                    // Also disable UsersClinicInfo
                    $existingClinicInfo = UsersClinicInfo::where('UserID', $user->UserID)->first();
                    if ($existingClinicInfo) {
                        $existingClinicInfo->update([
                            'IsDeleted' => true,
                            'LastUpdatedOn' => now(),
                            'LastUpdatedBy' => Auth::user()->UserID ?? 'System'
                        ]);
                    }
                }
            }

            Log::info('User updated successfully', ['user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'message' => 'User account updated successfully',
                'user' => $user
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating user', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->update([
                'IsDeleted' => true,
            ]);

            Log::info('User deleted successfully', ['user_id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting user', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleLock($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->locked = !$user->locked;
            $user->save();

            $status = $user->locked ? 'locked' : 'unlocked';

            Log::info("User $status successfully", ['user_id' => $id]);

            return response()->json([
                'success' => true,
                'message' => "User $status successfully",
                'locked' => $user->locked
            ]);

        } catch (\Exception $e) {
            Log::error('Error toggling user lock', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error toggling user lock: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleApproval($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->approved = !$user->approved;
            $user->save();

            $status = $user->approved ? 'approved' : 'unapproved';

            Log::info("User $status successfully", ['user_id' => $id]);

            return response()->json([
                'success' => true,
                'message' => "User $status successfully",
                'approved' => $user->approved
            ]);

        } catch (\Exception $e) {
            Log::error('Error toggling user approval', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error toggling user approval: ' . $e->getMessage()
            ], 500);
        }
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,UserID',
            'old_password' => 'required',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password'
        ]);

        try {
            $user = User::findOrFail($request->user_id);

            // Verify old password (you may need to adjust this based on your password hashing)
            if (!Hash::check($request->old_password, $user->Password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Old password is incorrect'
                ]);
            }

            // Update password
            $user->update([
                'Password' => Hash::make($request->new_password),
                'LastUpdatedOn' => now(),
                'LastUpdatedBy' => Auth::user()->Name ?? 'System'
            ]);

            Log::info('Password changed successfully', ['user_id' => $user->UserID]);

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error changing password', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error changing password: ' . $e->getMessage()
            ], 500);
        }
    }
}
