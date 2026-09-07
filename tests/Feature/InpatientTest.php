<?php

namespace Tests\Feature;

use App\Livewire\Inpatient\Admit;
use App\Livewire\Inpatient\Board;
use App\Models\Admission;
use App\Models\Bed;
use App\Models\Department;
use App\Models\Facility;
use App\Models\OpdVisit;
use App\Models\Patient;
use App\Models\User;
use App\Models\Ward;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InpatientTest extends TestCase
{
    use RefreshDatabase;

    private function makeVisit(): OpdVisit
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $facility = Facility::factory()->create();
        $department = Department::create(['facility_id' => $facility->id, 'name' => 'General OPD', 'code' => 'OPD']);
        $doctor = User::factory()->create(['facility_id' => $facility->id]);
        $doctor->assignRole('doctor');
        $patient = Patient::factory()->create(['facility_id' => $facility->id]);

        return OpdVisit::create([
            'facility_id' => $facility->id,
            'department_id' => $department->id,
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);
    }

    public function test_doctor_can_admit_patient_to_an_available_bed(): void
    {
        $visit = $this->makeVisit();
        $ward = Ward::create(['facility_id' => $visit->facility_id, 'name' => 'General Medical Ward', 'code' => 'GMW']);
        $bed = Bed::create(['facility_id' => $visit->facility_id, 'ward_id' => $ward->id, 'bed_number' => '1', 'status' => 'available']);

        Livewire::actingAs($visit->doctor)
            ->test(Admit::class, ['visit' => $visit])
            ->set('bed_id', (string) $bed->id)
            ->set('admission_reason', 'Severe malaria, needs IV treatment')
            ->call('save')
            ->assertRedirect(route('opd.consultation'));

        $this->assertDatabaseHas('admissions', [
            'opd_visit_id' => $visit->id,
            'patient_id' => $visit->patient_id,
            'bed_id' => $bed->id,
            'ward_id' => $ward->id,
            'status' => 'admitted',
        ]);

        $this->assertSame('occupied', $bed->fresh()->status);
    }

    public function test_cannot_admit_to_an_already_occupied_bed(): void
    {
        $visit = $this->makeVisit();
        $ward = Ward::create(['facility_id' => $visit->facility_id, 'name' => 'General Medical Ward', 'code' => 'GMW']);
        $bed = Bed::create(['facility_id' => $visit->facility_id, 'ward_id' => $ward->id, 'bed_number' => '1', 'status' => 'occupied']);

        Livewire::actingAs($visit->doctor)
            ->test(Admit::class, ['visit' => $visit])
            ->set('bed_id', (string) $bed->id)
            ->call('save')
            ->assertHasErrors('bed_id');

        $this->assertDatabaseCount('admissions', 0);
    }

    public function test_discharging_frees_the_bed(): void
    {
        $visit = $this->makeVisit();
        $ward = Ward::create(['facility_id' => $visit->facility_id, 'name' => 'General Medical Ward', 'code' => 'GMW']);
        $bed = Bed::create(['facility_id' => $visit->facility_id, 'ward_id' => $ward->id, 'bed_number' => '1', 'status' => 'occupied']);

        $admission = Admission::create([
            'facility_id' => $visit->facility_id,
            'patient_id' => $visit->patient_id,
            'ward_id' => $ward->id,
            'bed_id' => $bed->id,
            'opd_visit_id' => $visit->id,
            'admitting_doctor_id' => $visit->doctor_id,
            'status' => 'admitted',
            'admitted_at' => now(),
        ]);

        Livewire::actingAs($visit->doctor)
            ->test(Board::class)
            ->call('startDischarge', $admission->id)
            ->set('discharge_notes', 'Recovered, discharged home.')
            ->call('discharge');

        $this->assertDatabaseHas('admissions', [
            'id' => $admission->id,
            'status' => 'discharged',
        ]);

        $this->assertSame('available', $bed->fresh()->status);
    }

    public function test_user_without_permission_cannot_view_inpatient_board(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $facility = Facility::factory()->create();
        $receptionist = User::factory()->create(['facility_id' => $facility->id]);
        $receptionist->assignRole('receptionist');

        $this->actingAs($receptionist)
            ->get(route('inpatient.board'))
            ->assertForbidden();
    }
}
