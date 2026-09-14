<?php

namespace Tests\Feature;

use App\Livewire\Billing\Create as BillingCreate;
use App\Livewire\Insurance\Index as InsuranceIndex;
use App\Livewire\Insurance\Show as InsuranceShow;
use App\Models\Department;
use App\Models\Facility;
use App\Models\OpdVisit;
use App\Models\Patient;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InsuranceClaimsTest extends TestCase
{
    use RefreshDatabase;

    private function makeVisit(): OpdVisit
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $facility = Facility::factory()->create();
        $department = Department::create(['facility_id' => $facility->id, 'name' => 'General OPD', 'code' => 'OPD']);
        $doctor = User::factory()->create(['facility_id' => $facility->id]);
        $doctor->assignRole('doctor');
        $patient = Patient::factory()->create(['facility_id' => $facility->id, 'nhif_card_number' => '1234567890']);
        $receptionist = User::factory()->create(['facility_id' => $facility->id]);
        $receptionist->assignRole('receptionist');

        return OpdVisit::create([
            'facility_id' => $facility->id,
            'department_id' => $department->id,
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'status' => 'completed',
            'started_at' => now()->subMinutes(20),
            'ended_at' => now(),
        ]);
    }

    public function test_billing_creates_a_pending_claim_when_nhif_is_chosen_as_payer(): void
    {
        $visit = $this->makeVisit();
        $receptionist = User::where('facility_id', $visit->facility_id)->role('receptionist')->firstOrFail();

        Livewire::actingAs($receptionist)
            ->test(BillingCreate::class, ['visit' => $visit])
            ->set('items.0.description', 'Consultation fee')
            ->set('items.0.quantity', 1)
            ->set('items.0.unit_price', 10000)
            ->set('payer', 'insurance')
            ->set('nhifCardNumber', '1234567890')
            ->call('save')
            ->assertRedirect();

        $this->assertDatabaseHas('invoices', [
            'opd_visit_id' => $visit->id,
            'payment_method' => 'insurance',
        ]);

        $this->assertDatabaseHas('insurance_claims', [
            'patient_id' => $visit->patient_id,
            'nhif_card_number' => '1234567890',
            'status' => 'pending_eligibility',
            'amount_claimed' => 10000,
        ]);
    }

    public function test_eligibility_check_marks_a_valid_card_eligible(): void
    {
        $visit = $this->makeVisit();
        $invoice = $visit->patient->invoices()->create([
            'facility_id' => $visit->facility_id,
            'opd_visit_id' => $visit->id,
            'total' => 10000,
            'payment_method' => 'insurance',
        ]);
        $claim = $invoice->claim()->create([
            'facility_id' => $visit->facility_id,
            'patient_id' => $visit->patient_id,
            'nhif_card_number' => '1234567890',
            'amount_claimed' => 10000,
        ]);

        $officer = User::factory()->create(['facility_id' => $visit->facility_id]);
        $officer->assignRole('insurance-officer');

        Livewire::actingAs($officer)
            ->test(InsuranceShow::class, ['claim' => $claim])
            ->call('checkEligibility');

        $this->assertDatabaseHas('insurance_claims', [
            'id' => $claim->id,
            'status' => 'eligible',
            'scheme_name' => 'NHIF Standard Scheme',
        ]);
    }

    public function test_eligibility_check_rejects_a_malformed_card_number(): void
    {
        $visit = $this->makeVisit();
        $invoice = $visit->patient->invoices()->create([
            'facility_id' => $visit->facility_id,
            'opd_visit_id' => $visit->id,
            'total' => 10000,
            'payment_method' => 'insurance',
        ]);
        $claim = $invoice->claim()->create([
            'facility_id' => $visit->facility_id,
            'patient_id' => $visit->patient_id,
            'nhif_card_number' => 'not-a-card',
            'amount_claimed' => 10000,
        ]);

        $officer = User::factory()->create(['facility_id' => $visit->facility_id]);
        $officer->assignRole('insurance-officer');

        Livewire::actingAs($officer)
            ->test(InsuranceShow::class, ['claim' => $claim])
            ->call('checkEligibility');

        $this->assertSame('not_eligible', $claim->fresh()->status);
    }

    public function test_full_claim_lifecycle_from_submission_to_approval_records_a_payment(): void
    {
        $visit = $this->makeVisit();
        $invoice = $visit->patient->invoices()->create([
            'facility_id' => $visit->facility_id,
            'opd_visit_id' => $visit->id,
            'total' => 10000,
            'payment_method' => 'insurance',
        ]);
        $claim = $invoice->claim()->create([
            'facility_id' => $visit->facility_id,
            'patient_id' => $visit->patient_id,
            'nhif_card_number' => '1234567890',
            'amount_claimed' => 10000,
            'status' => 'eligible',
            'scheme_name' => 'NHIF Standard Scheme',
        ]);

        $officer = User::factory()->create(['facility_id' => $visit->facility_id]);
        $officer->assignRole('insurance-officer');

        $component = Livewire::actingAs($officer)
            ->test(InsuranceShow::class, ['claim' => $claim])
            ->call('submitClaim');

        $this->assertSame('submitted', $claim->fresh()->status);

        $component
            ->set('amountApproved', '10000')
            ->call('approve');

        $claim->refresh();
        $this->assertSame('approved', $claim->status);
        $this->assertSame('10000.00', $claim->amount_approved);

        $this->assertDatabaseHas('payments', [
            'invoice_id' => $invoice->id,
            'method' => 'insurance',
            'amount' => 10000,
            'reference' => $claim->claim_number,
        ]);

        $this->assertSame('paid', $invoice->fresh()->status);
    }

    public function test_claim_can_be_rejected_with_a_reason(): void
    {
        $visit = $this->makeVisit();
        $invoice = $visit->patient->invoices()->create([
            'facility_id' => $visit->facility_id,
            'opd_visit_id' => $visit->id,
            'total' => 10000,
            'payment_method' => 'insurance',
        ]);
        $claim = $invoice->claim()->create([
            'facility_id' => $visit->facility_id,
            'patient_id' => $visit->patient_id,
            'nhif_card_number' => '1234567890',
            'amount_claimed' => 10000,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $officer = User::factory()->create(['facility_id' => $visit->facility_id]);
        $officer->assignRole('insurance-officer');

        Livewire::actingAs($officer)
            ->test(InsuranceShow::class, ['claim' => $claim])
            ->set('rejectionReason', 'Card expired')
            ->call('reject');

        $this->assertDatabaseHas('insurance_claims', [
            'id' => $claim->id,
            'status' => 'rejected',
            'rejection_reason' => 'Card expired',
        ]);
    }

    public function test_user_without_permission_cannot_view_insurance_claims(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $facility = Facility::factory()->create();
        $nurse = User::factory()->create(['facility_id' => $facility->id]);
        $nurse->assignRole('nurse');

        $this->actingAs($nurse)
            ->get(route('insurance.index'))
            ->assertForbidden();
    }

    public function test_index_lists_claims_scoped_to_facility(): void
    {
        $visit = $this->makeVisit();
        $invoice = $visit->patient->invoices()->create([
            'facility_id' => $visit->facility_id,
            'opd_visit_id' => $visit->id,
            'total' => 10000,
            'payment_method' => 'insurance',
        ]);
        $claim = $invoice->claim()->create([
            'facility_id' => $visit->facility_id,
            'patient_id' => $visit->patient_id,
            'nhif_card_number' => '1234567890',
            'amount_claimed' => 10000,
        ]);

        $otherFacility = Facility::factory()->create();
        $otherPatient = Patient::factory()->create(['facility_id' => $otherFacility->id]);
        $otherInvoice = $otherPatient->invoices()->create([
            'facility_id' => $otherFacility->id,
            'total' => 5000,
            'payment_method' => 'insurance',
        ]);
        $otherFacilityClaim = $otherInvoice->claim()->create([
            'facility_id' => $otherFacility->id,
            'patient_id' => $otherPatient->id,
            'nhif_card_number' => '9999999999',
            'amount_claimed' => 5000,
        ]);

        $officer = User::factory()->create(['facility_id' => $visit->facility_id]);
        $officer->assignRole('insurance-officer');

        Livewire::actingAs($officer)
            ->test(InsuranceIndex::class)
            ->assertSee($claim->claim_number)
            ->assertDontSee($otherFacilityClaim->claim_number);
    }
}
