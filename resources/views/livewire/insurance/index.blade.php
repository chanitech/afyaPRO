<div>
    <x-adminlte-card title="NHIF Claims" icon="bi bi-shield-check">
        <div class="mb-3" style="max-width: 16rem;">
            <select wire:model.live="status" class="form-select">
                <option value="">All statuses</option>
                <option value="pending_eligibility">Pending Eligibility</option>
                <option value="eligible">Eligible</option>
                <option value="not_eligible">Not Eligible</option>
                <option value="submitted">Submitted</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Claim No.</th>
                        <th>Patient</th>
                        <th>Invoice</th>
                        <th class="text-end">Amount Claimed</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($claims as $claim)
                        <tr>
                            <td>{{ $claim->claim_number }}</td>
                            <td>{{ $claim->patient->fullName() }}</td>
                            <td>{{ $claim->invoice->invoice_number }}</td>
                            <td class="text-end">{{ number_format($claim->amount_claimed, 2) }}</td>
                            <td>
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
                            </td>
                            <td class="text-end">
                                <a href="{{ route('insurance.show', ['claim' => $claim->id]) }}" wire:navigate class="btn btn-outline-primary btn-sm">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No NHIF claims yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $claims->links() }}
    </x-adminlte-card>
</div>
