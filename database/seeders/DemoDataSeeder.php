<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\DiagnosticTest;
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

        $tests = [
            ['category' => 'lab', 'name' => 'Complete Blood Count (CBC)', 'code' => 'CBC', 'price' => 8000],
            ['category' => 'lab', 'name' => 'Malaria Rapid Diagnostic Test', 'code' => 'MRDT', 'price' => 5000],
            ['category' => 'lab', 'name' => 'Urinalysis', 'code' => 'UA', 'price' => 4000],
            ['category' => 'lab', 'name' => 'Blood Grouping & Rh Factor', 'code' => 'BG', 'price' => 6000],
            ['category' => 'lab', 'name' => 'HIV Rapid Test', 'code' => 'HIV', 'price' => 3000],
            ['category' => 'lab', 'name' => 'Fasting Blood Glucose', 'code' => 'FBG', 'price' => 4000],
            ['category' => 'radiology', 'name' => 'Chest X-Ray', 'code' => 'CXR', 'price' => 15000],
            ['category' => 'radiology', 'name' => 'Abdominal Ultrasound', 'code' => 'USS-ABD', 'price' => 25000],
        ];

        foreach ($tests as $test) {
            DiagnosticTest::firstOrCreate(
                ['facility_id' => $facility->id, 'name' => $test['name']],
                ['category' => $test['category'], 'code' => $test['code'], 'price' => $test['price'], 'is_active' => true]
            );
        }

        $this->command?->info("Demo facility '{$facility->name}' seeded with department '{$generalOpd->name}', 3 staff users, 5 patients, and ".count($tests).' diagnostic tests.');
    }
}
