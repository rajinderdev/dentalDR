<?php

namespace App\Http\Controllers;

use App\Models\LicenseModules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            
            // Debug: Log the incoming request data
            Log::info('Role permissions update request:', $request->all());
            
            foreach ($request->role_permissions as $roleName => $permissions) {
                Log::info("Processing role: $roleName with permissions: " . implode(', ', $permissions));
                
                $role = Role::where('name', $roleName)->first();
                
                if ($role) {
                    // Remove all existing permissions
                    $role->syncPermissions([]);
                    
                    // Add new permissions
                    if (!empty($permissions)) {
                        // Map module codes to permission names
                        $mappedPermissions = array_map(function($permission) {
                            $mapping = [
                                'TVP' => 'TVPLUS',
                                'UTI' => 'UTILITIES',
                                'LRS' => 'REPORTS_ANALYSIS', 
                                'RTS' => 'REPORTS_TX',
                                'ADS' => 'ADMIN',
                                'ACT' => 'APPOINTMENT',
                                'EXS' => 'EXPENSE',
                                'EIS' => 'EMAIL',
                                'APS' => 'APPOINTMENT',
                                'RAS' => 'REPORTS_ANALYSIS',
                                'BKP' => 'BACKUP',
                                'IVS' => 'INVENTORY',
                                'BAM' => 'BANK',
                                'EPR' => 'PRMS'
                            ];
                            
                            return $mapping[$permission] ?? $permission;
                        }, $permissions);
                        
                    Log::info("Mapped permissions for $roleName: " . implode(', ', $mappedPermissions));
                        
                        $role->givePermissionTo($mappedPermissions);
                    }
                } else {
                    Log::warning("Role not found: $roleName");
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Role permissions updated successfully'
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Error updating role permissions: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating role permissions: ' . $e->getMessage()
            ], 500);
        }
    }
}
