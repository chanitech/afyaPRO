<div>
    <h3 class="mb-3">Maternity</h3>

    @if (session('status'))
        <x-adminlte-alert theme="success" class="mb-3">
            {{ session('status') }}
        </x-adminlte-alert>
    @endif

    <x-adminlte-card icon="bi bi-heart-pulse-fill" title="In Labour" class="mb-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>G/P</th>
                        <th>Onset</th>
                        <th>Hours in Labour</th>
                        <th>Latest Dilation</th>
                        <th>Attending</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($inLabour as $delivery)
                        <tr>
                            <td>{{ $delivery->patient->fullName() }}</td>
                            <td>{{ $delivery->gravida ?? '—' }}/{{ $delivery->para ?? '—' }}</td>
                            <td>{{ $delivery->labour_onset_at->format('d M Y H:i') }}</td>
                            <td>{{ number_format($delivery->hoursSinceOnset(now()), 1) }}</td>
                            <td>
                                @php $latest = $delivery->observations->last(fn ($o) => $o->cervical_dilation_cm !== null); @endphp
                                {{ $latest?->cervical_dilation_cm !== null ? $latest->cervical_dilation_cm.' cm' : '—' }}
                            </td>
                            <td>{{ $delivery->attending?->name ?? '—' }}</td>
                            <td class="text-end">
                                <a href="{{ route('maternity.partograph', ['delivery' => $delivery->id]) }}" wire:navigate class="btn btn-outline-primary btn-sm">Partograph</a>
                                <a href="{{ route('maternity.deliver', ['delivery' => $delivery->id]) }}" wire:navigate class="btn btn-outline-success btn-sm">Record Delivery</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No patients currently in labour.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-adminlte-card>

    <x-adminlte-card icon="bi bi-clock-history" title="Recently Delivered">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Delivered</th>
                        <th>Mode</th>
                        <th>Outcome</th>
                        <th>Baby</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentlyDelivered as $delivery)
                        <tr>
                            <td>{{ $delivery->patient->fullName() }}</td>
                            <td>{{ $delivery->delivered_at?->format('d M Y H:i') }}</td>
                            <td>{{ str($delivery->delivery_mode)->headline() }}</td>
                            <td>{{ str($delivery->outcome)->headline() }}</td>
                            <td>
                                @if ($delivery->baby_sex)
                                    {{ ucfirst($delivery->baby_sex) }}@if ($delivery->baby_weight_grams), {{ $delivery->baby_weight_grams }}g @endif
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No deliveries recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-adminlte-card>
</div>
