<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables as YajraDataTables;
class ManageUserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = User::with(['roles']);

            return YajraDataTables::of($query)
                ->addColumn('action', function($user) {
                    return view('admin.users.actions', compact('user'))->render();
                })
                ->editColumn('id', function($user) {
                    return $user->UserID;
                })
                ->editColumn('UserType', function($user) {
                    return  Role::where('RoleID', $user->RoleID)->first()->name;
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
                ->editColumn('created_at', function($user) {
                    return $user->CreatedOn ? $user->CreatedOn->format('M d, Y h:i A') : 'N/A';
                })
                ->addColumn('customer', function($user) {
                    return $user->Name ?? 'N/A';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('admin.users.index');
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
                'CreatedBy' => auth()->user()->Name ?? 'System'
            ]);

            // Assign role to user
            $role = Role::find($request->RoleID);
            if ($role) {
                $user->assignRole($role->RoleName);
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
        return view('admin.manage-account', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'security_answer' => 'required|string|max:255',
            'approved' => 'required|boolean',
            'locked' => 'required|boolean',
            'security_question' => 'required|string|max:255',
            'user_type' => 'required|string|in:admin,doctor,nurse,receptionist,lab_staff,accountant'
        ]);

        // Password validation only if provided
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:8|confirmed'
            ]);
        }

        try {
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'security_answer' => $request->security_answer,
                'approved' => $request->approved,
                'locked' => $request->locked,
                'security_question' => $request->security_question,
                'user_type' => $request->user_type,
                'updated_at' => now(),
            ];

            // Update password only if provided
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

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
            $user->delete();

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
}
