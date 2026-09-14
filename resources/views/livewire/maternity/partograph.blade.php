<div>
    <x-adminlte-card icon="bi bi-heart-pulse-fill"
        title="Partograph — {{ $delivery->patient->fullName() }} ({{ $delivery->patient->patient_number }})" class="mb-3">
        <x-slot name="tools">
            @if ($delivery->status === 'in_labour')
                <a href="{{ route('maternity.deliver', ['delivery' => $delivery->id]) }}" wire:navigate class="btn btn-success btn-sm">
                    <i class="bi bi-clipboard2-pulse"></i> Record Delivery
                </a>
            @else
                <span class="badge text-bg-success">Delivered {{ $delivery->delivered_at?->format('d M Y H:i') }}</span>
            @endif
        </x-slot>

        <p class="text-muted mb-0">
            G{{ $delivery->gravida ?? '?' }}P{{ $delivery->para ?? '?' }}
            &middot; Onset {{ $delivery->labour_onset_at->format('d M Y H:i') }}
            &middot; Attending {{ $delivery->attending?->name ?? '—' }}
            @if ($delivery->expected_delivery_date)
                &middot; EDD {{ $delivery->expected_delivery_date->format('d M Y') }}
            @endif
        </p>
    </x-adminlte-card>

    <div class="row g-3">
        <div class="col-12 col-lg-7">
            <x-adminlte-card title="Cervicograph" icon="bi bi-graph-up">
                <p class="text-muted small mb-2">Cervical dilation vs. hours since labour onset. Dashed lines are the WHO alert (left) and action (right) lines, anchored at 4cm.</p>
                <svg viewBox="0 0 480 350" style="width: 100%; max-width: 480px; background: var(--bs-body-bg);">
                    @for ($cm = 0; $cm <= 10; $cm += 2)
                        @php $y = $chart['originY'] - ($cm / 10) * $chart['plotH']; @endphp
                        <line x1="{{ $chart['originX'] }}" y1="{{ $y }}" x2="{{ $chart['originX'] + $chart['plotW'] }}" y2="{{ $y }}" stroke="#f1f3f5" />
                        <text x="30" y="{{ $y + 4 }}" font-size="10" fill="#495057">{{ $cm }}</text>
                    @endfor

                    <line x1="{{ $chart['originX'] }}" y1="{{ $chart['originY'] }}" x2="{{ $chart['originX'] + $chart['plotW'] }}" y2="{{ $chart['originY'] }}" stroke="#495057" />
                    <line x1="{{ $chart['originX'] }}" y1="{{ $chart['originY'] }}" x2="{{ $chart['originX'] }}" y2="{{ $chart['originY'] - $chart['plotH'] }}" stroke="#495057" />
                    <text x="{{ $chart['originX'] + $chart['plotW'] / 2 }}" y="342" font-size="11" fill="#495057" text-anchor="middle">Hours since labour onset</text>
                    <text x="14" y="{{ $chart['originY'] - $chart['plotH'] / 2 }}" font-size="11" fill="#495057" text-anchor="middle" transform="rotate(-90, 14, {{ $chart['originY'] - $chart['plotH'] / 2 }})">Cervical dilation (cm)</text>

                    @if ($chart['alertLine'])
                        <line x1="{{ $chart['alertLine'][0]['x'] }}" y1="{{ $chart['alertLine'][0]['y'] }}" x2="{{ $chart['alertLine'][1]['x'] }}" y2="{{ $chart['alertLine'][1]['y'] }}" stroke="#f59f00" stroke-dasharray="5,4" stroke-width="1.5" />
                        <text x="{{ $chart['alertLine'][1]['x'] - 20 }}" y="{{ $chart['alertLine'][1]['y'] - 6 }}" font-size="10" fill="#f59f00">Alert</text>
                    @endif
                    @if ($chart['actionLine'])
                        <line x1="{{ $chart['actionLine'][0]['x'] }}" y1="{{ $chart['actionLine'][0]['y'] }}" x2="{{ $chart['actionLine'][1]['x'] }}" y2="{{ $chart['actionLine'][1]['y'] }}" stroke="#e03131" stroke-dasharray="5,4" stroke-width="1.5" />
                        <text x="{{ $chart['actionLine'][1]['x'] - 24 }}" y="{{ $chart['actionLine'][1]['y'] - 6 }}" font-size="10" fill="#e03131">Action</text>
                    @endif

                    @if (count($chart['dilationPoints']) > 1)
                        <polyline points="@foreach ($chart['dilationPoints'] as $p){{ $p['x'] }},{{ $p['y'] }} @endforeach" fill="none" stroke="#0d6efd" stroke-width="2" />
                    @endif
                    @foreach ($chart['dilationPoints'] as $p)
                        <circle cx="{{ $p['x'] }}" cy="{{ $p['y'] }}" r="4" fill="#0d6efd" />
                    @endforeach

                    @if (empty($chart['dilationPoints']))
                        <text x="240" y="170" font-size="12" fill="#adb5bd" text-anchor="middle">No dilation readings recorded yet</text>
                    @endif
                </svg>
            </x-adminlte-card>

            <x-adminlte-card title="Observations" icon="bi bi-list-ul" class="mt-3">
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>FHR</th>
                                <th>Dilation</th>
                                <th>Descent</th>
                                <th>Contractions</th>
                                <th>Liquor</th>
                                <th>BP</th>
                                <th>Pulse</th>
                                <th>Temp</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($delivery->observations as $observation)
                                <tr>
                                    <td>{{ $observation->recorded_at->format('H:i') }}</td>
                                    <td>{{ $observation->fetal_heart_rate ?? '—' }}</td>
                                    <td>{{ $observation->cervical_dilation_cm !== null ? $observation->cervical_dilation_cm.'cm' : '—' }}</td>
                                    <td>{{ $observation->descent_fifths ?? '—' }}</td>
                                    <td>{{ $observation->contractions_per_10min ?? '—' }}</td>
                                    <td>{{ $observation->liquor ? str($observation->liquor)->headline() : '—' }}</td>
                                    <td>
                                        @if ($observation->maternal_bp_systolic)
                                            {{ $observation->maternal_bp_systolic }}/{{ $observation->maternal_bp_diastolic }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>{{ $observation->maternal_pulse ?? '—' }}</td>
                                    <td>{{ $observation->maternal_temperature ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-3">No observations recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-adminlte-card>
        </div>

        <div class="col-12 col-lg-5">
            @if ($delivery->status === 'in_labour')
                <x-adminlte-card title="Record Observation" icon="bi bi-plus-circle">
                    <form wire:submit="addObservation">
                        <div class="row g-2">
                            <div class="col-12">
                                <x-input-label for="recorded_at" value="Time" />
                                <x-text-input type="datetime-local" wire:model="recorded_at" id="recorded_at" />
                                <x-input-error :messages="$errors->get('recorded_at')" class="mt-1" />
                            </div>
                            <div class="col-6">
                                <x-input-label for="fetal_heart_rate" value="FHR (bpm)" />
                                <x-text-input type="number" wire:model="fetal_heart_rate" id="fetal_heart_rate" />
                            </div>
                            <div class="col-6">
                                <x-input-label for="cervical_dilation_cm" value="Dilation (cm)" />
                                <x-text-input type="number" min="0" max="10" wire:model="cervical_dilation_cm" id="cervical_dilation_cm" />
                            </div>
                            <div class="col-6">
                                <x-input-label for="descent_fifths" value="Descent (/5)" />
                                <x-text-input type="number" min="0" max="5" wire:model="descent_fifths" id="descent_fifths" />
                            </div>
                            <div class="col-6">
                                <x-input-label for="contractions_per_10min" value="Contractions/10min" />
                                <x-text-input type="number" min="0" max="10" wire:model="contractions_per_10min" id="contractions_per_10min" />
                            </div>
                            <div class="col-6">
                                <x-input-label for="liquor" value="Liquor" />
                                <select wire:model="liquor" id="liquor" class="form-select">
                                    <option value="">—</option>
                                    <option value="intact">Intact</option>
                                    <option value="clear">Clear</option>
                                    <option value="meconium">Meconium</option>
                                    <option value="blood_stained">Blood Stained</option>
                                    <option value="absent">Absent</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <x-input-label for="moulding" value="Moulding" />
                                <select wire:model="moulding" id="moulding" class="form-select">
                                    <option value="">—</option>
                                    <option value="0">0</option>
                                    <option value="+">+</option>
                                    <option value="++">++</option>
                                    <option value="+++">+++</option>
                                </select>
                            </div>
                            <div class="col-4">
                                <x-input-label for="maternal_pulse" value="Pulse" />
                                <x-text-input type="number" wire:model="maternal_pulse" id="maternal_pulse" />
                            </div>
                            <div class="col-4">
                                <x-input-label for="maternal_bp_systolic" value="BP Sys" />
                                <x-text-input type="number" wire:model="maternal_bp_systolic" id="maternal_bp_systolic" />
                            </div>
                            <div class="col-4">
                                <x-input-label for="maternal_bp_diastolic" value="BP Dia" />
                                <x-text-input type="number" wire:model="maternal_bp_diastolic" id="maternal_bp_diastolic" />
                            </div>
                            <div class="col-6">
                                <x-input-label for="maternal_temperature" value="Temp (°C)" />
                                <x-text-input type="number" step="0.1" wire:model="maternal_temperature" id="maternal_temperature" />
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <x-primary-button>Add Observation</x-primary-button>
                        </div>
                    </form>
                </x-adminlte-card>
            @endif
        </div>
    </div>
</div>
