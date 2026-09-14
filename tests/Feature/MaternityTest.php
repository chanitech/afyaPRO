<?php

namespace Tests\Feature;

use App\Livewire\Maternity\Admit;
use App\Livewire\Maternity\Deliver;
use App\Livewire\Maternity\Partograph;
use App\Models\Delivery;
use App\Models\Department;
use App\Models\Facility;
use App\Models\OpdVisit;
use App\Models\Patient;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MaternityTest extends TestCase
{
    use RefreshDatabase;

    private function makeVisit(): OpdVisit
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $facility = Facility::factory()->create();
        $department = Department::create(['facility_id' => $facility->id, 'name' => 'Maternity OPD', 'code' => 'MAT-OPD']);
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

    public function test_doctor_can_start_labour_for_a_visit(): void
    {
        $visit = $this->makeVisit();

        Livewire::actingAs($visit->doctor)
            ->test(Admit::class, ['visit' => $visit])
            ->set('gravida', '3')
            ->set('para', '2')
            ->set('labour_onset_at', now()->format('Y-m-d\TH:i'))
            ->call('save')
            ->assertRedirect();

        $this->assertDatabaseHas('deliveries', [
            'opd_visit_id' => $visit->id,
            'patient_id' => $visit->patient_id,
            'gravida' => 3,
            'para' => 2,
            'status' => 'in_labour',
        ]);
    }

    public function test_observations_can_be_recorded_and_feed_the_cervicograph(): void
    {
        $visit = $this->makeVisit();

        $delivery = Delivery::create([
            'facility_id' => $visit->facility_id,
            'patient_id' => $visit->patient_id,
            'opd_visit_id' => $visit->id,
            'attending_id' => $visit->doctor_id,
            'labour_onset_at' => now()->subHours(3),
            'status' => 'in_labour',
        ]);

        $component = Livewire::actingAs($visit->doctor)
            ->test(Partograph::class, ['delivery' => $delivery])
            ->set('recorded_at', now()->format('Y-m-d\TH:i'))
            ->set('fetal_heart_rate', '140')
            ->set('cervical_dilation_cm', '5')
            ->set('maternal_pulse', '82')
            ->set('maternal_bp_systolic', '110')
            ->set('maternal_bp_diastolic', '70')
            ->call('addObservation');

        $this->assertDatabaseHas('partograph_observations', [
            'delivery_id' => $delivery->id,
            'fetal_heart_rate' => 140,
            'cervical_dilation_cm' => 5,
        ]);

        $chart = $component->viewData('chart');
        $this->assertCount(1, $chart['dilationPoints']);
        // Dilation already >= 4cm, so the alert/action reference lines are anchored.
        $this->assertNotNull($chart['alertLine']);
        $this->assertNotNull($chart['actionLine']);
    }

    public function test_recording_delivery_marks_it_delivered(): void
    {
        $visit = $this->makeVisit();

        $delivery = Delivery::create([
            'facility_id' => $visit->facility_id,
            'patient_id' => $visit->patient_id,
            'opd_visit_id' => $visit->id,
            'attending_id' => $visit->doctor_id,
            'labour_onset_at' => now()->subHours(6),
            'status' => 'in_labour',
        ]);

        Livewire::actingAs($visit->doctor)
            ->test(Deliver::class, ['delivery' => $delivery])
            ->set('delivery_mode', 'spontaneous_vaginal')
            ->set('delivered_at', now()->format('Y-m-d\TH:i'))
            ->set('outcome', 'live_birth')
            ->set('baby_sex', 'female')
            ->set('baby_weight_grams', '3200')
            ->set('apgar_1min', '9')
            ->set('apgar_5min', '10')
            ->call('save')
            ->assertRedirect(route('maternity.board'));

        $this->assertDatabaseHas('deliveries', [
            'id' => $delivery->id,
            'status' => 'delivered',
            'delivery_mode' => 'spontaneous_vaginal',
            'outcome' => 'live_birth',
            'baby_weight_grams' => 3200,
        ]);
    }

    public function test_user_without_maternity_permission_cannot_view_board(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $facility = Facility::factory()->create();
        $receptionist = User::factory()->create(['facility_id' => $facility->id]);
        $receptionist->assignRole('receptionist');

        $this->actingAs($receptionist)
            ->get(route('maternity.board'))
            ->assertForbidden();
    }
}
