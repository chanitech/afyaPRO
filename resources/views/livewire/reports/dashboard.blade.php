<div>
    <x-adminlte-card title="Hospital Operations Dashboard" icon="bi bi-graph-up">
        <x-slot name="tools">
            <div class="btn-group btn-group-sm me-2">
                <button type="button" wire:click="setPeriod('this_month')" class="btn btn-outline-secondary">This Month</button>
                <button type="button" wire:click="setPeriod('last_30')" class="btn btn-outline-secondary">Last 30 Days</button>
                <button type="button" wire:click="setPeriod('last_90')" class="btn btn-outline-secondary">Last 90 Days</button>
            </div>
            <input type="date" wire:model.live="startDate" class="form-control form-control-sm d-inline-block" style="width: 9rem;">
            <span class="mx-1">&ndash;</span>
            <input type="date" wire:model.live="endDate" class="form-control form-control-sm d-inline-block" style="width: 9rem;">
        </x-slot>

        <div class="row g-3 mb-4">
            <div class="col-6 col-md-2">
                <div class="border rounded p-3 text-center h-100">
                    <div class="text-muted small">Bed Occupancy</div>
                    <div class="fs-4 fw-semibold">{{ number_format($overall['occupancy'], 1) }}%</div>
                    <div class="text-muted small">{{ $currentOccupied }}/{{ $currentTotal }} occupied now</div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="border rounded p-3 text-center h-100">
                    <div class="text-muted small">ALOS</div>
                    <div class="fs-4 fw-semibold">{{ $overall['alos'] !== null ? number_format($overall['alos'], 1) : '—' }}</div>
                    <div class="text-muted small">days</div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="border rounded p-3 text-center h-100">
                    <div class="text-muted small">Bed Turnover</div>
                    <div class="fs-4 fw-semibold">{{ $overall['bed_turnover_rate'] !== null ? number_format($overall['bed_turnover_rate'], 2) : '—' }}</div>
                    <div class="text-muted small">discharges/bed</div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="border rounded p-3 text-center h-100">
                    <div class="text-muted small">Turnover Interval</div>
                    <div class="fs-4 fw-semibold">{{ $overall['turnover_interval'] !== null ? number_format($overall['turnover_interval'], 1) : '—' }}</div>
                    <div class="text-muted small">days empty/bed</div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="border rounded p-3 text-center h-100">
                    <div class="text-muted small">OPD Visit Rate</div>
                    <div class="fs-4 fw-semibold">{{ number_format($opdVisitRate, 1) }}</div>
                    <div class="text-muted small">visits/day ({{ $opdVisits }} total)</div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="border rounded p-3 text-center h-100">
                    <div class="text-muted small">Discharges</div>
                    <div class="fs-4 fw-semibold">{{ $overall['discharges'] }}</div>
                    <div class="text-muted small">in period</div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12 col-lg-6">
                <h6>Barber-Johnson Chart</h6>
                <p class="text-muted small mb-2">Turnover Interval (x) vs Average Length of Stay (y), by ward. Diagonal lines mark constant occupancy.</p>
                <svg viewBox="0 0 480 380" style="width: 100%; max-width: 480px; background: var(--bs-body-bg);">
                    @foreach ($chart['occupancyLines'] as $line)
                        <line x1="{{ $line['x1'] }}" y1="{{ $line['y1'] }}" x2="{{ $line['x2'] }}" y2="{{ $line['y2'] }}" stroke="#adb5bd" stroke-dasharray="4,3" />
                        <text x="{{ $line['x2'] + 2 }}" y="{{ $line['y2'] }}" font-size="10" fill="#adb5bd">{{ $line['label'] }}</text>
                    @endforeach

                    <line x1="{{ $chart['originX'] }}" y1="{{ $chart['originY'] }}" x2="{{ $chart['originX'] + $chart['plotW'] }}" y2="{{ $chart['originY'] }}" stroke="#495057" />
                    <line x1="{{ $chart['originX'] }}" y1="{{ $chart['originY'] }}" x2="{{ $chart['originX'] }}" y2="{{ $chart['originY'] - $chart['plotH'] }}" stroke="#495057" />
                    <text x="{{ $chart['originX'] + $chart['plotW'] / 2 }}" y="370" font-size="11" fill="#495057" text-anchor="middle">Turnover Interval (days)</text>
                    <text x="14" y="{{ $chart['originY'] - $chart['plotH'] / 2 }}" font-size="11" fill="#495057" text-anchor="middle" transform="rotate(-90, 14, {{ $chart['originY'] - $chart['plotH'] / 2 }})">ALOS (days)</text>

                    @foreach ($chart['points'] as $point)
                        <circle cx="{{ $point['cx'] }}" cy="{{ $point['cy'] }}" r="5" fill="#0d6efd" />
                        <text x="{{ $point['cx'] + 8 }}" y="{{ $point['cy'] + 4 }}" font-size="11" fill="#212529">{{ $point['label'] }}</text>
                    @endforeach

                    @if (empty($chart['points']))
                        <text x="240" y="190" font-size="12" fill="#adb5bd" text-anchor="middle">No discharges in this period</text>
                    @endif
                </svg>
            </div>

            <div class="col-12 col-lg-6">
                <h6>By Ward</h6>
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>Ward</th>
                                <th class="text-end">Beds</th>
                                <th class="text-end">Occupancy</th>
                                <th class="text-end">ALOS</th>
                                <th class="text-end">Discharges</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($wards as $ward)
                                <tr>
                                    <td>{{ $ward['name'] }}</td>
                                    <td class="text-end">{{ $ward['beds'] }}</td>
                                    <td class="text-end">{{ number_format($ward['occupancy'], 1) }}%</td>
                                    <td class="text-end">{{ $ward['alos'] !== null ? number_format($ward['alos'], 1) : '—' }}</td>
                                    <td class="text-end">{{ $ward['discharges'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No wards configured.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </x-adminlte-card>
</div>
