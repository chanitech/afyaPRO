<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\Patient;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientIdentificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_receptionist_can_view_a_patient_id_card_with_a_barcode(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $facility = Facility::factory()->create();
        $receptionist = User::factory()->create(['facility_id' => $facility->id]);
        $receptionist->assignRole('receptionist');
        $patient = Patient::factory()->create([
            'facility_id' => $facility->id,
            'first_name' => 'Juana',
            'last_name' => 'Kerluke',
            'patient_number' => 'PT-2026-E3GIRU',
        ]);

        $response = $this->actingAs($receptionist)->get(route('patients.id-card', ['patient' => $patient->id]));

        $response->assertOk();
        $response->assertSee('Juana Kerluke');
        $response->assertSee('*PT-2026-E3GIRU*', false);
    }

    public function test_receptionist_can_view_a_patient_wristband_with_a_barcode(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $facility = Facility::factory()->create();
        $receptionist = User::factory()->create(['facility_id' => $facility->id]);
        $receptionist->assignRole('receptionist');
        $patient = Patient::factory()->create([
            'facility_id' => $facility->id,
            'patient_number' => 'PT-2026-ABCDEF',
        ]);

        $response = $this->actingAs($receptionist)->get(route('patients.wristband', ['patient' => $patient->id]));

        $response->assertOk();
        $response->assertSee('*PT-2026-ABCDEF*', false);
    }

    public function test_user_without_patients_permission_cannot_print_identification(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $facility = Facility::factory()->create();
        $doctor = User::factory()->create(['facility_id' => $facility->id]);
        $doctor->assignRole('doctor');
        $patient = Patient::factory()->create(['facility_id' => $facility->id]);

        $this->actingAs($doctor)
            ->get(route('patients.id-card', ['patient' => $patient->id]))
            ->assertForbidden();

        $this->actingAs($doctor)
            ->get(route('patients.wristband', ['patient' => $patient->id]))
            ->assertForbidden();
    }
}
