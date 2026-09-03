<div>
    <x-adminlte-card title="Visits Awaiting Billing" icon="bi bi-receipt">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Completed</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Diagnosis</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($visits as $visit)
                        <tr>
                            <td>{{ $visit->ended_at?->format('d M Y H:i') }}</td>
                            <td>{{ $visit->patient->fullName() }}</td>
                            <td>{{ $visit->doctor->name }}</td>
                            <td>{{ str($visit->diagnosis)->limit(40) }}</td>
                            <td class="text-end">
                                <a href="{{ route('billing.create', ['visit' => $visit->id]) }}" wire:navigate class="btn btn-outline-primary btn-sm">Create Invoice</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No visits awaiting billing.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $visits->links() }}
    </x-adminlte-card>
</div>
