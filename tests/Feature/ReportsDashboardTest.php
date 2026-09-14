<?php

namespace Tests\Feature;

use App\Livewire\Reports\Dashboard;
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

class ReportsDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_computes_bed_occupancy_and_length_of_stay_for_the_period(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $facility = Facility::factory()->create();
        $admin = User::factory()->create(['facility_id' => $facility->id]);
        $admin->assignRole('admin');

        $ward = Ward::create(['facility_id' => $facility->id, 'name' => 'General Medical Ward', 'code' => 'GMW']);
        $bedA = Bed::create(['facility_id' => $facility->id, 'ward_id' => $ward->id, 'bed_number' => '1', 'status' => 'occupied']);
        $bedB = Bed::create(['facility_id' => $facility->id, 'ward_id' => $ward->id, 'bed_number' => '2', 'status' => 'available']);

        $patient1 = Patient::factory()->create(['facility_id' => $facility->id]);
        $patient2 = Patient::factory()->create(['facility_id' => $facility->id]);

        // Both admissions have a 4-day length of stay, fully inside the reporting window.
        Admission::create([
            'facility_id' => $facility->id,
            'patient_id' => $patient1->id,
            'ward_id' => $ward->id,
            'bed_id' => $bedA->id,
            'admitting_doctor_id' => $admin->id,
            'status' => 'discharged',
            'admitted_at' => now()->subDays(9),
            'discharged_at' => now()->subDays(5),
        ]);
        Admission::create([
            'facility_id' => $facility->id,
            'patient_id' => $patient2->id,
            'ward_id' => $ward->id,
            'bed_id' => $bedB->id,
            'admitting_doctor_id' => $admin->id,
            'status' => 'discharged',
            'admitted_at' => now()->subDays(6),
            'discharged_at' => now()->subDays(2),
        ]);

        $department = Department::create(['facility_id' => $facility->id, 'name' => 'General OPD', 'code' => 'OPD']);
        for ($i = 0; $i < 5; $i++) {
            OpdVisit::create([
                'facility_id' => $facility->id,
                'department_id' => $department->id,
                'patient_id' => $patient1->id,
                'doctor_id' => $admin->id,
                'status' => 'completed',
                'started_at' => now()->subDays(3),
                'ended_at' => now()->subDays(3)->addMinutes(20),
            ]);
        }

        $component = Livewire::actingAs($admin)
            ->test(Dashboard::class)
            ->set('startDate', now()->subDays(9)->toDateString())
            ->set('endDate', now()->toDateString());

        // 2 beds x 10 days = 20 available bed-days; 4 + 4 = 8 occupied bed-days -> 40% occupancy.
        $component->assertSet('startDate', now()->subDays(9)->toDateString());

        $overall = $component->viewData('overall');
        $this->assertEqualsWithDelta(40.0, $overall['occupancy'], 0.5);
        $this->assertEqualsWithDelta(4.0, $overall['alos'], 0.1);
        $this->assertEqualsWithDelta(6.0, $overall['turnover_interval'], 0.5);
        $this->assertEqualsWithDelta(1.0, $overall['bed_turnover_rate'], 0.01);
        $this->assertSame(2, $overall['discharges']);

        $this->assertSame(5, $component->viewData('opdVisits'));
    }

    public function test_user_without_reports_permission_cannot_view_dashboard(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $facility = Facility::factory()->create();
        $receptionist = User::factory()->create(['facility_id' => $facility->id]);
        $receptionist->assignRole('receptionist');

        $this->actingAs($receptionist)
            ->get(route('reports.dashboard'))
            ->assertForbidden();
    }
}
