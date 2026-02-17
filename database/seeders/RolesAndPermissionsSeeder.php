<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\LicenseModules;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Clean up existing web guard roles and all permissions to prevent conflicts
        Role::where('guard_name', 'web')->delete();
        Permission::query()->delete(); // Delete all permissions
        
        // Create permissions for each module for both guards
        $modules = [
            'PRMS' => 'Patient Record Management System',
            'DMS' => 'Document Management System',
            'ADMIN' => 'Administration System',
            'SMS' => 'SMS Integration System',
            'EXPENSE' => 'Expense Management System',
            'REPORTS_TX' => 'Reports Transaction Analysis',
            'BACKUP' => 'Online Backup Management',
            'INVENTORY' => 'Inventory System',
            'TVPLUS' => 'TV Plus',
            'APPOINTMENT' => 'Appointment System',
            'ACCOUNTS' => 'Accounts',
            'EMAIL' => 'Email Integration System',
            'LAB' => 'Lab Record System',
            'BANK' => 'Bank Accounts Management',
            'REPORTS_ANALYSIS' => 'Reports Analysis System',
            'EDI' => 'Electronic Data Interface',
            'UTILITIES' => 'Utilities'
        ];

        // Create permissions for both web and api guards
        foreach ($modules as $code => $name) {
            Permission::firstOrCreate(['name' => $code, 'guard_name' => 'web']);
            Permission::firstOrCreate(['name' => $code, 'guard_name' => 'api']);
        }

        // Create roles with proper RoleID
        $roles = [
            'Administrator' => 'ECD75F1D-1232-4309-B8E9-A5293EC09999',
            'Staff' => '7C7D137C-8327-4D70-9812-042AF4CDA299',
            'Doctor' => '5973E31F-A329-4E2D-8541-C98D1E22CF99',
            'Accounts' => 'ACC12345-6789-4321-ABCD-EFG123456789',
            'Stockkeeper' => 'STK12345-6789-4321-ABCD-EFG123456780',
            'Labstaff' => 'LAB12345-6789-4321-ABCD-EFG123456781'
        ];

        foreach ($roles as $roleName => $roleId) {
            // Create new role with RoleID (web guard only)
            $role = new Role();
            $role->RoleID = $roleId;
            $role->name = $roleName;
            $role->guard_name = 'web';
            $role->save();
        }

        // Get roles
        $adminRole = Role::where('name', 'Administrator')->where('guard_name', 'web')->first();
        $staffRole = Role::where('name', 'Staff')->where('guard_name', 'web')->first();
        $doctorRole = Role::where('name', 'Doctor')->where('guard_name', 'web')->first();
        $accountsRole = Role::where('name', 'Accounts')->where('guard_name', 'web')->first();
        $stockkeeperRole = Role::where('name', 'Stockkeeper')->where('guard_name', 'web')->first();
        $labstaffRole = Role::where('name', 'Labstaff')->where('guard_name', 'web')->first();

        // Give all permissions to administrator
        if ($adminRole) {
            $adminRole->syncPermissions(array_keys($modules));
        }

        // Give specific permissions to staff (from image)
        if ($staffRole) {
            $staffPermissions = [
                'PRMS', 'DMS', 'APPOINTMENT', 'ACCOUNTS', 'LAB'
            ];
            $staffRole->syncPermissions($staffPermissions);
        }

        // Give specific permissions to doctor (from image)
        if ($doctorRole) {
            $doctorPermissions = [
                'PRMS', 'DMS', 'SMS', 'APPOINTMENT', 'ACCOUNTS', 'EMAIL', 'LAB', 'UTILITIES'
            ];
            $doctorRole->syncPermissions($doctorPermissions);
        }

        // Give specific permissions to accounts (from image)
        if ($accountsRole) {
            $accountsPermissions = [
                'REPORTS_TX', 'APPOINTMENT', 'ACCOUNTS'
            ];
            $accountsRole->syncPermissions($accountsPermissions);
        }

        // Give specific permissions to stockkeeper
        if ($stockkeeperRole) {
            $stockkeeperPermissions = [
                'INVENTORY', 'ACCOUNTS', 'EXPENSE'
            ];
            $stockkeeperRole->syncPermissions($stockkeeperPermissions);
        }

        // Give specific permissions to labstaff
        if ($labstaffRole) {
            $labstaffPermissions = [
                'LAB', 'DMS', 'PRMS'
            ];
            $labstaffRole->syncPermissions($labstaffPermissions);
        }

        // Populate LicenseModules table
        foreach ($modules as $code => $name) {
            LicenseModules::firstOrCreate([
                'ModuleCode' => $code,
                'ModuleName' => $name
            ], [
                'ModuleDescription' => $name,
                'OrderNumber' => array_search($code, array_keys($modules)) + 1,
                'CreatedOn' => now(),
                'CreatedBy' => 'system'
            ]);
        }
    }
}
