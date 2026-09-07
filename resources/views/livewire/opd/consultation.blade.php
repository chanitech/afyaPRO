<div>
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h3 class="mb-0">OPD Consultation</h3>
        <select wire:model.live="departmentId" class="form-select w-auto">
            @foreach ($departments as $department)
                <option value="{{ $department->id }}">{{ $department->name }}</option>
            @endforeach
        </select>
    </div>

    @if (session('status'))
        <x-adminlte-alert theme="success" class="mb-3">
            {{ session('status') }}
        </x-adminlte-alert>
    @endif

    <div class="row g-3">
        <div class="col-md-4">
            <x-adminlte-card title="Called Patients" icon="bi bi-people">
                <ul class="list-group list-group-flush">
                    @forelse ($inService as $ticket)
                        <li class="list-group-item d-flex align-items-center justify-content-between">
                            <div>
                                <span class="fw-semibold">#{{ $ticket->ticket_number }}</span>
                                <span class="ms-2 small">{{ $ticket->patient->fullName() }}</span>
                            </div>
                            @if ($activeVisit && $activeVisit->patient_id === $ticket->patient_id)
                                <span class="badge text-bg-primary">Open</span>
                            @elseif ($ticket->status === 'called')
                                <button wire:click="start({{ $ticket->id }})" class="btn btn-outline-primary btn-sm">Start</button>
                            @else
                                <button wire:click="start({{ $ticket->id }})" class="btn btn-outline-secondary btn-sm">Resume</button>
                            @endif
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted py-4">No patients called yet.</li>
                    @endforelse
                </ul>
            </x-adminlte-card>
        </div>

        <div class="col-md-8">
            @if ($activeVisit)
                <x-adminlte-card icon="bi bi-heart-pulse"
                    title="Consultation — {{ $activeVisit->patient->fullName() }} ({{ $activeVisit->patient->patient_number }})">
                    <x-slot name="tools">
                        <a href="{{ route('diagnostics.order', ['visit' => $activeVisit->id]) }}" wire:navigate class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-clipboard2-pulse"></i> Order Tests
                        </a>
                        <a href="{{ route('pharmacy.prescribe', ['visit' => $activeVisit->id]) }}" wire:navigate class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-capsule"></i> Prescribe
                        </a>
                        @if (! $activeVisit->admission)
                            <a href="{{ route('inpatient.admit', ['visit' => $activeVisit->id]) }}" wire:navigate class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-hospital"></i> Admit to Ward
                            </a>
                        @endif
                    </x-slot>

                    @if ($activeVisit->admission)
                        <div class="alert alert-info d-flex align-items-center justify-content-between mb-3">
                            <span>
                                <i class="bi bi-hospital me-1"></i>
                                Admitted to {{ $activeVisit->admission->ward->name }}, Bed {{ $activeVisit->admission->bed->bed_number }}
                            </span>
                            @if ($activeVisit->admission->status === 'discharged')
                                <span class="badge text-bg-secondary">Discharged</span>
                            @else
                                <span class="badge text-bg-success">Admitted</span>
                            @endif
                        </div>
                    @endif

                    @if ($activeVisit->diagnosticOrders->isNotEmpty())
                        <div class="mb-3">
                            <h6 class="text-uppercase text-muted small mb-2">Ordered Tests</h6>
                            <ul class="list-group list-group-flush">
                                @foreach ($activeVisit->diagnosticOrders as $diagnosticOrder)
                                    @foreach ($diagnosticOrder->items as $item)
                                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                                            <span>{{ $item->test->name }}</span>
                                            @if ($item->status === 'completed')
                                                <span class="badge text-bg-success">{{ $item->result_value }}</span>
                                            @else
                                                <span class="badge text-bg-secondary">Pending</span>
                                            @endif
                                        </li>
                                    @endforeach
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($activeVisit->prescriptions->isNotEmpty())
                        <div class="mb-3">
                            <h6 class="text-uppercase text-muted small mb-2">Prescriptions</h6>
                            <ul class="list-group list-group-flush">
                                @foreach ($activeVisit->prescriptions as $prescription)
                                    @foreach ($prescription->items as $item)
                                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                                            <span>{{ $item->drug->name }} &times;{{ $item->quantity }}</span>
                                            @if ($item->status === 'dispensed')
                                                <span class="badge text-bg-success">Dispensed</span>
                                            @else
                                                <span class="badge text-bg-secondary">Pending</span>
                                            @endif
                                        </li>
                                    @endforeach
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form wire:submit="complete">
                        <div class="mb-3">
                            <x-input-label for="chief_complaint" value="Chief complaint" />
                            <textarea wire:model="chief_complaint" id="chief_complaint" rows="2" class="form-control"></textarea>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-6 col-md-3">
                                <x-input-label for="temperature" value="Temp (°C)" />
                                <x-text-input wire:model="temperature" id="temperature" />
                            </div>
                            <div class="col-6 col-md-3">
                                <x-input-label for="blood_pressure" value="BP" />
                                <x-text-input wire:model="blood_pressure" id="blood_pressure" placeholder="120/80" />
                            </div>
                            <div class="col-6 col-md-3">
                                <x-input-label for="pulse" value="Pulse" />
                                <x-text-input wire:model="pulse" id="pulse" />
                            </div>
                            <div class="col-6 col-md-3">
                                <x-input-label for="weight" value="Weight (kg)" />
                                <x-text-input wire:model="weight" id="weight" />
                            </div>
                        </div>

                        <div class="mb-3">
                            <x-input-label for="diagnosis" value="Diagnosis" />
                            <textarea wire:model="diagnosis" id="diagnosis" rows="2" class="form-control"></textarea>
                        </div>

                        <div class="mb-3">
                            <x-input-label for="notes" value="Notes / treatment plan" />
                            <textarea wire:model="notes" id="notes" rows="3" class="form-control"></textarea>
                        </div>

                        <div class="d-flex justify-content-end">
                            <x-primary-button>Complete Visit &amp; Bill</x-primary-button>
                        </div>
                    </form>
                </x-adminlte-card>
            @else
                <x-adminlte-card>
                    <div class="text-center text-muted py-5">
                        Select a called patient to start the consultation.
                    </div>
                </x-adminlte-card>
            @endif
        </div>
    </div>
</div>
