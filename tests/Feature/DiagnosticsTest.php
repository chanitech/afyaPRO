<?php

namespace Tests\Feature;

use App\Livewire\Diagnostics\Order;
use App\Livewire\Diagnostics\Show;
use App\Models\Department;
use App\Models\DiagnosticOrder;
use App\Models\DiagnosticTest as DiagnosticTestModel;
use App\Models\Facility;
use App\Models\OpdVisit;
use App\Models\Patient;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DiagnosticsTest extends TestCase
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

    public function test_doctor_can_order_tests_for_a_visit(): void
    {
        $visit = $this->makeVisit();
        $test = DiagnosticTestModel::create([
            'facility_id' => $visit->facility_id,
            'category' => 'lab',
            'name' => 'Malaria Rapid Test',
            'price' => 5000,
        ]);

        Livewire::actingAs($visit->doctor)
            ->test(Order::class, ['visit' => $visit])
            ->set("selected.{$test->id}", true)
            ->call('save')
            ->assertRedirect(route('opd.consultation'));

        $this->assertDatabaseHas('diagnostic_orders', [
            'opd_visit_id' => $visit->id,
            'patient_id' => $visit->patient_id,
            'status' => 'ordered',
        ]);

        $this->assertDatabaseHas('diagnostic_order_items', [
            'diagnostic_test_id' => $test->id,
            'price' => 5000,
            'status' => 'pending',
        ]);
    }

    public function test_ordering_with_no_tests_selected_fails_validation(): void
    {
        $visit = $this->makeVisit();

        Livewire::actingAs($visit->doctor)
            ->test(Order::class, ['visit' => $visit])
            ->call('save')
            ->assertHasErrors('selected');

        $this->assertDatabaseCount('diagnostic_orders', 0);
    }

    public function test_lab_tech_can_record_a_result_and_order_completes(): void
    {
        $visit = $this->makeVisit();
        $test = DiagnosticTestModel::create([
            'facility_id' => $visit->facility_id,
            'category' => 'lab',
            'name' => 'Malaria Rapid Test',
            'price' => 5000,
        ]);

        $order = DiagnosticOrder::create([
            'facility_id' => $visit->facility_id,
            'patient_id' => $visit->patient_id,
            'opd_visit_id' => $visit->id,
            'ordered_by' => $visit->doctor_id,
            'status' => 'ordered',
            'ordered_at' => now(),
        ]);

        $item = $order->items()->create([
            'diagnostic_test_id' => $test->id,
            'price' => 5000,
            'status' => 'pending',
        ]);

        $labTech = User::factory()->create(['facility_id' => $visit->facility_id]);
        $labTech->assignRole('lab-tech');

        Livewire::actingAs($labTech)
            ->test(Show::class, ['order' => $order])
            ->set("resultValue.{$item->id}", 'Negative')
            ->call('recordResult', $item->id);

        $this->assertDatabaseHas('diagnostic_order_items', [
            'id' => $item->id,
            'status' => 'completed',
            'result_value' => 'Negative',
            'resulted_by' => $labTech->id,
        ]);

        $this->assertDatabaseHas('diagnostic_orders', [
            'id' => $order->id,
            'status' => 'completed',
        ]);
    }

    public function test_user_without_permission_cannot_view_diagnostics(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $facility = Facility::factory()->create();
        $receptionist = User::factory()->create(['facility_id' => $facility->id]);
        $receptionist->assignRole('receptionist');

        $this->actingAs($receptionist)
            ->get(route('diagnostics.pending'))
            ->assertForbidden();
    }
}
