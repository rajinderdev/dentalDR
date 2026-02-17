<?php

namespace App\Http\Controllers;

use App\Models\LicenseModules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ManageRoleController extends Controller
{
    public function index()
    {
        // Get all roles with web guard only
        $roles = Role::where('guard_name', 'web')->orderBy('name')->get();
        
        // Get all license modules
        $modules = LicenseModules::orderBy('OrderNumber')->get();
        
        // Get role permissions mapping
        $rolePermissions = [];
        foreach ($roles as $role) {
            $permissions = $role->permissions->pluck('name')->toArray();
            $rolePermissions[$role->name] = $permissions;
        }
        
        return view('admin.manage-roles', compact('roles', 'modules', 'rolePermissions'));
    }
    
    public function update(Request $request)
    {
        $request->validate([
            'role_permissions' => 'required|array',
            'role_permissions.*' => 'array',
            'role_permissions.*.*' => 'string'
        ]);
        
        try {
            DB::beginTransaction();
            
            foreach ($request->role_permissions as $roleName => $permissions) {
                $role = Role::where('name', $roleName)->first();
                
                if ($role) {
                    // Remove all existing permissions
                    $role->syncPermissions([]);
                    
                    // Add new permissions
                    if (!empty($permissions)) {
                        $role->givePermissionTo($permissions);
                    }
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Role permissions updated successfully'
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating role permissions: ' . $e->getMessage()
            ], 500);
        }
    }
}
