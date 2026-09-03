<div>
    <x-adminlte-card title="Diagnostic Orders" icon="bi bi-clipboard2-pulse">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Ordered</th>
                        <th>Patient</th>
                        <th>Ordered By</th>
                        <th>Tests</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td>{{ $order->ordered_at->format('d M Y H:i') }}</td>
                            <td>{{ $order->patient->fullName() }}</td>
                            <td>{{ $order->orderedBy->name }}</td>
                            <td>{{ $order->items->pluck('test.name')->join(', ') }}</td>
                            <td>
                                <span @class([
                                    'badge',
                                    'text-bg-warning' => $order->status === 'ordered',
                                    'text-bg-info' => $order->status === 'in_progress',
                                ])>
                                    {{ str($order->status)->headline() }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('diagnostics.show', ['order' => $order->id]) }}" wire:navigate class="btn btn-outline-primary btn-sm">Process</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No diagnostic orders pending.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $orders->links() }}
    </x-adminlte-card>
</div>
