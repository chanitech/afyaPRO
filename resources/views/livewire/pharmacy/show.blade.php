<div class="row">
    <div class="col-12 col-lg-9">
        <x-adminlte-card icon="bi bi-capsule"
            title="Prescription — {{ $prescription->patient->fullName() }} ({{ $prescription->patient->patient_number }})">
            <x-slot name="tools">
                <span @class([
                    'badge',
                    'text-bg-warning' => $prescription->status === 'prescribed',
                    'text-bg-info' => $prescription->status === 'partially_dispensed',
                    'text-bg-success' => $prescription->status === 'dispensed',
                ])>
                    {{ str($prescription->status)->headline() }}
                </span>
            </x-slot>

            <p class="text-muted mb-4">
                Prescribed by {{ $prescription->prescribedBy->name }} on {{ $prescription->prescribed_at->format('d M Y H:i') }}
            </p>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Drug</th>
                            <th>Qty</th>
                            <th>Dosage</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prescription->items as $item)
                            <tr>
                                <td>{{ $item->drug->name }}</td>
                                <td>{{ $item->quantity }} {{ $item->drug->unit }}</td>
                                <td>{{ $item->dosage_instructions ?: '—' }}</td>
                                <td>
                                    @if ($item->status === 'dispensed')
                                        <span class="badge text-bg-success">Dispensed</span>
                                    @else
                                        <span class="badge text-bg-secondary">Pending</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if ($item->status === 'dispensed')
                                        <span class="small text-muted">
                                            by {{ $item->dispensedBy?->name }} on {{ $item->dispensed_at?->format('d M Y H:i') }}
                                        </span>
                                    @else
                                        <button type="button" wire:click="dispense({{ $item->id }})" class="btn btn-primary btn-sm">
                                            Dispense
                                        </button>
                                    @endif
                                    <x-input-error :messages="$errors->get('stock.'.$item->id)" class="mt-1" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-adminlte-card>
    </div>
</div>
