<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['Super Admin', 'Admin', 'Doctor', 'Pathologist', 'Receptionist', 'Accountant', 'Lab Technician', 'Patient'];

        $permissions = [
            'dashboard.view', 'patients.manage', 'doctors.manage', 'appointments.manage',
            'tests.manage', 'reports.manage', 'billing.manage', 'prescriptions.manage',
            'employees.manage', 'notifications.manage', 'roles.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        foreach ($roles as $role) {
            $r = Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
            if ($role === 'Super Admin' || $role === 'Admin') {
                $r->syncPermissions($permissions);
            }
        }
    }
}
