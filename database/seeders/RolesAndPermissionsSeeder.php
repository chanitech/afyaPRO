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
        'insurance.manage',
        'diagnostics.manage',
        'pharmacy.manage',
        'inpatient.manage',
        'maternity.manage',
        'reports.manage',
        'users.manage',
        'facilities.manage',
    ];

    public const ROLES = [
        'admin' => self::PERMISSIONS,
        'receptionist' => ['patients.manage', 'appointments.manage', 'queue.manage', 'billing.manage', 'insurance.manage'],
        'doctor' => ['queue.manage', 'opd.manage', 'diagnostics.manage', 'pharmacy.manage', 'inpatient.manage', 'maternity.manage'],
        'nurse' => ['queue.manage', 'opd.manage', 'diagnostics.manage', 'pharmacy.manage', 'inpatient.manage', 'maternity.manage'],
        'lab-tech' => ['diagnostics.manage'],
        'pharmacist' => ['pharmacy.manage'],
        'insurance-officer' => ['billing.manage', 'insurance.manage'],
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
