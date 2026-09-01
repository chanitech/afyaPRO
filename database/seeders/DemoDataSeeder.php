<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Facility;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $facility = Facility::firstOrCreate(
            ['code' => 'MNH-DEMO'],
            [
                'name' => 'Muhimbili National Hospital (Demo)',
                'type' => 'national',
                'phone' => '+255222151367',
                'email' => 'info@mnh.demo',
                'address' => 'Kalenga Street, Upanga, Dar es Salaam',
                'is_active' => true,
            ]
        );

        $generalOpd = Department::firstOrCreate(
            ['facility_id' => $facility->id, 'code' => 'OPD'],
            ['name' => 'General Outpatient Department']
        );

        Department::firstOrCreate(
            ['facility_id' => $facility->id, 'code' => 'PEDS'],
            ['name' => 'Pediatrics']
        );

        $admin = User::firstOrCreate(
            ['email' => 'admin@afyapro.test'],
            [
                'name' => 'Facility Admin',
                'facility_id' => $facility->id,
                'phone' => '+255700000001',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles(['admin']);

        $doctor = User::firstOrCreate(
            ['email' => 'doctor@afyapro.test'],
            [
                'name' => 'Dr. Amina Juma',
                'facility_id' => $facility->id,
                'phone' => '+255700000002',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $doctor->syncRoles(['doctor']);

        $receptionist = User::firstOrCreate(
            ['email' => 'reception@afyapro.test'],
            [
                'name' => 'Grace Mushi',
                'facility_id' => $facility->id,
                'phone' => '+255700000003',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $receptionist->syncRoles(['receptionist']);

        if (Patient::where('facility_id', $facility->id)->count() === 0) {
            Patient::factory()
                ->count(5)
                ->create(['facility_id' => $facility->id, 'registered_by' => $receptionist->id]);
        }

        $this->command?->info("Demo facility '{$facility->name}' seeded with department '{$generalOpd->name}', 3 staff users, and 5 patients.");
    }
}
