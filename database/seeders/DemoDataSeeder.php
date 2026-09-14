<?php

namespace Database\Seeders;

use App\Models\Bed;
use App\Models\Department;
use App\Models\DiagnosticTest;
use App\Models\Drug;
use App\Models\Facility;
use App\Models\Patient;
use App\Models\User;
use App\Models\Ward;
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

        $pharmacist = User::firstOrCreate(
            ['email' => 'pharmacist@afyapro.test'],
            [
                'name' => 'Baraka Mnyika',
                'facility_id' => $facility->id,
                'phone' => '+255700000004',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $pharmacist->syncRoles(['pharmacist']);

        $nurse = User::firstOrCreate(
            ['email' => 'nurse@afyapro.test'],
            [
                'name' => 'Faraja Kessy',
                'facility_id' => $facility->id,
                'phone' => '+255700000005',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $nurse->syncRoles(['nurse']);

        $insuranceOfficer = User::firstOrCreate(
            ['email' => 'nhif@afyapro.test'],
            [
                'name' => 'Zainab Rashidi',
                'facility_id' => $facility->id,
                'phone' => '+255700000006',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $insuranceOfficer->syncRoles(['insurance-officer']);

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

        $drugs = [
            ['name' => 'Paracetamol 500mg', 'generic_name' => 'Paracetamol', 'form' => 'tablet', 'unit' => 'tablet', 'price' => 100, 'stock' => 2000, 'reorder' => 200],
            ['name' => 'Amoxicillin 500mg', 'generic_name' => 'Amoxicillin', 'form' => 'capsule', 'unit' => 'capsule', 'price' => 300, 'stock' => 800, 'reorder' => 100],
            ['name' => 'Artemether/Lumefantrine (ALU)', 'generic_name' => 'Artemether/Lumefantrine', 'form' => 'tablet', 'unit' => 'course', 'price' => 3500, 'stock' => 150, 'reorder' => 30],
            ['name' => 'Oral Rehydration Salts (ORS)', 'generic_name' => 'ORS', 'form' => 'sachet', 'unit' => 'sachet', 'price' => 500, 'stock' => 300, 'reorder' => 50],
            ['name' => 'Diclofenac 50mg', 'generic_name' => 'Diclofenac', 'form' => 'tablet', 'unit' => 'tablet', 'price' => 150, 'stock' => 600, 'reorder' => 100],
            ['name' => 'Metronidazole 400mg', 'generic_name' => 'Metronidazole', 'form' => 'tablet', 'unit' => 'tablet', 'price' => 120, 'stock' => 500, 'reorder' => 80],
            ['name' => 'Amlodipine 5mg', 'generic_name' => 'Amlodipine', 'form' => 'tablet', 'unit' => 'tablet', 'price' => 200, 'stock' => 400, 'reorder' => 60],
            ['name' => 'IV Normal Saline 500ml', 'generic_name' => 'Sodium Chloride 0.9%', 'form' => 'infusion', 'unit' => 'bottle', 'price' => 4000, 'stock' => 100, 'reorder' => 20],
        ];

        foreach ($drugs as $drug) {
            Drug::firstOrCreate(
                ['facility_id' => $facility->id, 'name' => $drug['name']],
                [
                    'generic_name' => $drug['generic_name'],
                    'form' => $drug['form'],
                    'unit' => $drug['unit'],
                    'unit_price' => $drug['price'],
                    'quantity_on_hand' => $drug['stock'],
                    'reorder_level' => $drug['reorder'],
                    'is_active' => true,
                ]
            );
        }

        $wards = [
            ['name' => 'General Medical Ward', 'code' => 'GMW', 'type' => 'general', 'beds' => 6],
            ['name' => 'Maternity Ward', 'code' => 'MAT', 'type' => 'maternity', 'beds' => 4],
            ['name' => 'Intensive Care Unit', 'code' => 'ICU', 'type' => 'icu', 'beds' => 2],
        ];

        foreach ($wards as $wardData) {
            $ward = Ward::firstOrCreate(
                ['facility_id' => $facility->id, 'code' => $wardData['code']],
                ['name' => $wardData['name'], 'ward_type' => $wardData['type']]
            );

            for ($i = 1; $i <= $wardData['beds']; $i++) {
                Bed::firstOrCreate(
                    ['ward_id' => $ward->id, 'bed_number' => (string) $i],
                    ['facility_id' => $facility->id, 'status' => 'available']
                );
            }
        }

        $this->command?->info("Demo facility '{$facility->name}' seeded with department '{$generalOpd->name}', 5 staff users, 5 patients, ".count($tests).' diagnostic tests, '.count($drugs).' drugs, and '.count($wards).' wards.');
    }
}
