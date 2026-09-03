<?php

namespace Tests\Feature;

use App\Livewire\Pharmacy\Prescribe;
use App\Livewire\Pharmacy\Show;
use App\Models\Department;
use App\Models\Drug;
use App\Models\Facility;
use App\Models\OpdVisit;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PharmacyTest extends TestCase
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

    public function test_doctor_can_prescribe_drugs_for_a_visit(): void
    {
        $visit = $this->makeVisit();
        $drug = Drug::create([
            'facility_id' => $visit->facility_id,
            'name' => 'Paracetamol 500mg',
            'unit' => 'tablet',
            'unit_price' => 100,
            'quantity_on_hand' => 500,
            'reorder_level' => 50,
        ]);

        Livewire::actingAs($visit->doctor)
            ->test(Prescribe::class, ['visit' => $visit])
            ->set('items.0.drug_id', (string) $drug->id)
            ->set('items.0.quantity', 10)
            ->set('items.0.dosage_instructions', '1 tablet twice daily')
            ->call('save')
            ->assertRedirect(route('opd.consultation'));

        $this->assertDatabaseHas('prescriptions', [
            'opd_visit_id' => $visit->id,
            'patient_id' => $visit->patient_id,
            'status' => 'prescribed',
        ]);

        $this->assertDatabaseHas('prescription_items', [
            'drug_id' => $drug->id,
            'quantity' => 10,
            'unit_price' => 100,
            'status' => 'pending',
        ]);

        // Stock is untouched until dispensing.
        $this->assertSame(500, $drug->fresh()->quantity_on_hand);
    }

    public function test_dispensing_decrements_stock_and_completes_prescription(): void
    {
        $visit = $this->makeVisit();
        $drug = Drug::create([
            'facility_id' => $visit->facility_id,
            'name' => 'Amoxicillin 500mg',
            'unit' => 'capsule',
            'unit_price' => 300,
            'quantity_on_hand' => 20,
            'reorder_level' => 5,
        ]);

        $prescription = Prescription::create([
            'facility_id' => $visit->facility_id,
            'patient_id' => $visit->patient_id,
            'opd_visit_id' => $visit->id,
            'prescribed_by' => $visit->doctor_id,
            'status' => 'prescribed',
            'prescribed_at' => now(),
        ]);

        $item = $prescription->items()->create([
            'drug_id' => $drug->id,
            'quantity' => 15,
            'unit_price' => 300,
            'status' => 'pending',
        ]);

        $pharmacist = User::factory()->create(['facility_id' => $visit->facility_id]);
        $pharmacist->assignRole('pharmacist');

        Livewire::actingAs($pharmacist)
            ->test(Show::class, ['prescription' => $prescription])
            ->call('dispense', $item->id);

        $this->assertDatabaseHas('prescription_items', [
            'id' => $item->id,
            'status' => 'dispensed',
            'dispensed_by' => $pharmacist->id,
        ]);

        $this->assertDatabaseHas('prescriptions', [
            'id' => $prescription->id,
            'status' => 'dispensed',
        ]);

        $this->assertSame(5, $drug->fresh()->quantity_on_hand);

        $this->assertDatabaseHas('drug_stock_movements', [
            'drug_id' => $drug->id,
            'type' => 'dispense',
            'quantity_change' => -15,
        ]);
    }

    public function test_dispensing_more_than_stock_on_hand_fails(): void
    {
        $visit = $this->makeVisit();
        $drug = Drug::create([
            'facility_id' => $visit->facility_id,
            'name' => 'IV Normal Saline',
            'unit' => 'bottle',
            'unit_price' => 4000,
            'quantity_on_hand' => 2,
            'reorder_level' => 5,
        ]);

        $prescription = Prescription::create([
            'facility_id' => $visit->facility_id,
            'patient_id' => $visit->patient_id,
            'opd_visit_id' => $visit->id,
            'prescribed_by' => $visit->doctor_id,
            'status' => 'prescribed',
            'prescribed_at' => now(),
        ]);

        $item = $prescription->items()->create([
            'drug_id' => $drug->id,
            'quantity' => 5,
            'unit_price' => 4000,
            'status' => 'pending',
        ]);

        $pharmacist = User::factory()->create(['facility_id' => $visit->facility_id]);
        $pharmacist->assignRole('pharmacist');

        Livewire::actingAs($pharmacist)
            ->test(Show::class, ['prescription' => $prescription])
            ->call('dispense', $item->id)
            ->assertHasErrors('stock.'.$item->id);

        $this->assertSame(2, $drug->fresh()->quantity_on_hand);
        $this->assertDatabaseHas('prescription_items', ['id' => $item->id, 'status' => 'pending']);
    }

    public function test_user_without_permission_cannot_view_pharmacy(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $facility = Facility::factory()->create();
        $receptionist = User::factory()->create(['facility_id' => $facility->id]);
        $receptionist->assignRole('receptionist');

        $this->actingAs($receptionist)
            ->get(route('pharmacy.pending'))
            ->assertForbidden();
    }
}
