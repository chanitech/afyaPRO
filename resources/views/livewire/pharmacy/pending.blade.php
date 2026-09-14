<div>
    <x-adminlte-card title="Prescriptions" icon="bi bi-capsule">
        <x-slot name="tools">
            <a href="{{ route('pharmacy.drugs') }}" wire:navigate class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-boxes"></i> Drug Catalog
            </a>
        </x-slot>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Prescribed</th>
                        <th>Patient</th>
                        <th>Prescribed By</th>
                        <th>Drugs</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($prescriptions as $prescription)
                        <tr>
                            <td>{{ $prescription->prescribed_at->format('d M Y H:i') }}</td>
                            <td>{{ $prescription->patient->fullName() }}</td>
                            <td>{{ $prescription->prescribedBy->name }}</td>
                            <td>{{ $prescription->items->pluck('drug.name')->join(', ') }}</td>
                            <td>
                                <span @class([
                                    'badge',
                                    'text-bg-warning' => $prescription->status === 'prescribed',
                                    'text-bg-info' => $prescription->status === 'partially_dispensed',
                                ])>
                                    {{ str($prescription->status)->headline() }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('pharmacy.show', ['prescription' => $prescription->id]) }}" wire:navigate class="btn btn-outline-primary btn-sm">Dispense</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No prescriptions pending.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $prescriptions->links() }}
    </x-adminlte-card>
</div>
