<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'create-admin', 'create-assignee', 'manage-categories', 'manage-locations',
            'manage-system-settings', 'view-audit-logs', 'view-reports',
            'manage-inventory', 'issue-return', 'manage-maintenance', 'manage-backup',
            'view-own-assignments',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions($permissions); // full access

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions([
            'manage-inventory', 'issue-return', 'create-assignee',
            'view-reports', 'manage-maintenance',
        ]);

        $assignee = Role::firstOrCreate(['name' => 'assignee', 'guard_name' => 'web']);
        $assignee->syncPermissions(['view-own-assignments']);
    }
}
