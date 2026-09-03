<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public const PERMISSIONS = [
        'patients.manage',
        'appointments.manage',
        'queue.manage',
        'opd.manage',
        'billing.manage',
        'diagnostics.manage',
        'users.manage',
        'facilities.manage',
    ];

    public const ROLES = [
        'admin' => self::PERMISSIONS,
        'receptionist' => ['patients.manage', 'appointments.manage', 'queue.manage', 'billing.manage'],
        'doctor' => ['queue.manage', 'opd.manage', 'diagnostics.manage'],
        'nurse' => ['queue.manage', 'opd.manage', 'diagnostics.manage'],
        'lab-tech' => ['diagnostics.manage'],
    ];

    public function run(): void
    {
        foreach (self::PERMISSIONS as $permission) {
            Permission::findOrCreate($permission);
        }

        foreach (self::ROLES as $role => $permissions) {
            Role::findOrCreate($role)->syncPermissions($permissions);
        }
    }
}
