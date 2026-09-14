<div class="row">
    <div class="col-12 col-lg-8">
        <x-adminlte-card icon="bi bi-shield-check"
            title="{{ $claim->insurerLabel() }} Claim {{ $claim->claim_number }}">
            <x-slot name="tools">
                <span @class([
                    'badge',
                    'text-bg-secondary' => $claim->status === 'pending_eligibility',
                    'text-bg-info' => $claim->status === 'eligible',
                    'text-bg-danger' => in_array($claim->status, ['not_eligible', 'rejected']),
                    'text-bg-primary' => $claim->status === 'submitted',
                    'text-bg-success' => $claim->status === 'approved',
                ])>
                    {{ str($claim->status)->headline() }}
                </span>
            </x-slot>

            <p class="text-muted mb-3">
                {{ $claim->patient->fullName() }} &middot; {{ $claim->patient->patient_number }}
                &middot; <a href="{{ route('billing.show', ['invoice' => $claim->invoice_id]) }}" wire:navigate>Invoice {{ $claim->invoice->invoice_number }}</a>
            </p>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <div class="text-muted small">{{ $claim->insurerLabel() }} Member Number</div>
                    <div class="fw-medium">{{ $claim->member_number }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Scheme</div>
                    <div class="fw-medium">{{ $claim->scheme_name ?? '—' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Amount Claimed</div>
                    <div class="fw-medium">{{ number_format($claim->amount_claimed, 2) }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Amount Approved</div>
                    <div class="fw-medium">{{ $claim->amount_approved !== null ? number_format($claim->amount_approved, 2) : '—' }}</div>
                </div>
            </div>

            @if (in_array($claim->status, ['not_eligible', 'rejected']) && $claim->rejection_reason)
                <x-adminlte-alert theme="danger" icon="bi bi-exclamation-triangle" class="mb-3">
                    {{ $claim->rejection_reason }}
                </x-adminlte-alert>
            @endif

            <div class="border-top pt-3">
                @if ($claim->status === 'pending_eligibility')
                    <button type="button" wire:click="checkEligibility" class="btn btn-primary">
                        Check {{ $claim->insurerLabel() }} Eligibility
                    </button>
                @elseif ($claim->status === 'not_eligible')
                    <button type="button" wire:click="checkEligibility" class="btn btn-outline-primary">
                        Re-check Eligibility
                    </button>
                @elseif ($claim->status === 'eligible')
                    <button type="button" wire:click="submitClaim" class="btn btn-primary">
                        Submit Claim to {{ $claim->insurerLabel() }}
                    </button>
                @elseif ($claim->status === 'submitted')
                    <p class="text-muted mb-3">
                        Submitted {{ $claim->submitted_at?->format('d M Y H:i') }}
                        by {{ $claim->submittedBy?->name }}. Record the payer's decision once received.
                    </p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <form wire:submit="approve" class="d-flex align-items-end gap-2">
                                <div class="flex-grow-1">
                                    <x-input-label for="amountApproved" value="Approve amount" />
                                    <x-text-input type="number" min="0" step="0.01" wire:model="amountApproved" id="amountApproved" />
                                    <x-input-error :messages="$errors->get('amountApproved')" class="mt-1" />
                                </div>
                                <x-primary-button>Approve</x-primary-button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <form wire:submit="reject" class="d-flex align-items-end gap-2">
                                <div class="flex-grow-1">
                                    <x-input-label for="rejectionReason" value="Rejection reason" />
                                    <x-text-input wire:model="rejectionReason" id="rejectionReason" />
                                    <x-input-error :messages="$errors->get('rejectionReason')" class="mt-1" />
                                </div>
                                <button type="submit" class="btn btn-outline-danger">Reject</button>
                            </form>
                        </div>
                    </div>
                @elseif ($claim->status === 'approved')
                    <p class="text-success mb-0">
                        Approved for {{ number_format($claim->amount_approved, 2) }} on {{ $claim->responded_at?->format('d M Y H:i') }}.
                        Payment has been recorded on the invoice.
                    </p>
                @endif
            </div>
        </x-adminlte-card>
    </div>
</div>
