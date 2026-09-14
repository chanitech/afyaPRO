<?php

namespace App\Livewire\Insurance;

use App\Models\InsuranceClaim;
use App\Support\Nhif\NhifGateway;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Show extends Component
{
    public InsuranceClaim $claim;

    #[Validate('numeric|min:0')]
    public string $amountApproved = '';

    #[Validate('nullable|string|max:255')]
    public string $rejectionReason = '';

    public function mount(InsuranceClaim $claim): void
    {
        $this->claim = $claim->load(['patient', 'invoice.items', 'submittedBy']);
        $this->amountApproved = (string) $this->claim->amount_claimed;
    }

    public function checkEligibility(): void
    {
        $result = app(NhifGateway::class)->checkEligibility($this->claim->nhif_card_number);

        $this->claim->update([
            'status' => $result->eligible ? 'eligible' : 'not_eligible',
            'scheme_name' => $result->schemeName,
            'rejection_reason' => $result->eligible ? null : $result->message,
            'eligibility_checked_at' => now(),
        ]);
    }

    public function submitClaim(): void
    {
        if ($this->claim->status !== 'eligible') {
            return;
        }

        app(NhifGateway::class)->submitClaim(
            $this->claim->claim_number,
            $this->claim->nhif_card_number,
            (float) $this->claim->amount_claimed,
        );

        $this->claim->update([
            'status' => 'submitted',
            'submitted_at' => now(),
            'submitted_by' => Auth::id(),
        ]);
    }

    public function approve(): void
    {
        $this->validate(['amountApproved' => 'required|numeric|min:0']);

        $amount = min((float) $this->amountApproved, $this->claim->invoice->balanceDue());

        $this->claim->update([
            'status' => 'approved',
            'amount_approved' => $amount,
            'responded_at' => now(),
        ]);

        $this->claim->invoice->payments()->create([
            'amount' => $amount,
            'method' => 'insurance',
            'reference' => $this->claim->claim_number,
            'received_by' => Auth::id(),
            'paid_at' => now(),
        ]);

        $this->claim->refresh();
    }

    public function reject(): void
    {
        $this->validate(['rejectionReason' => 'required|string|max:255']);

        $this->claim->update([
            'status' => 'rejected',
            'rejection_reason' => $this->rejectionReason,
            'responded_at' => now(),
        ]);
    }

    public function render()
    {
        return view('livewire.insurance.show');
    }
}
