<div>
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h3 class="mb-0">Inpatient Ward Board</h3>
        <a href="{{ route('inpatient.wards') }}" wire:navigate class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-hospital"></i> Manage Wards &amp; Beds
        </a>
    </div>

    @if (session('status'))
        <x-adminlte-alert theme="success" class="mb-3">
            {{ session('status') }}
        </x-adminlte-alert>
    @endif

    @forelse ($admissions as $wardName => $wardAdmissions)
        <x-adminlte-card icon="bi bi-hospital" class="mb-3" :title="$wardName">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Bed</th>
                            <th>Patient</th>
                            <th>Admitted</th>
                            <th>Admitting Doctor</th>
                            <th>Reason</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($wardAdmissions as $admission)
                            <tr>
                                <td>{{ $admission->bed->bed_number }}</td>
                                <td>{{ $admission->patient->fullName() }}</td>
                                <td>{{ $admission->admitted_at->format('d M Y H:i') }}</td>
                                <td>{{ $admission->admittingDoctor->name }}</td>
                                <td>{{ str($admission->admission_reason)->limit(40) ?: '—' }}</td>
                                <td class="text-end">
                                    <button type="button" wire:click="startDischarge({{ $admission->id }})" class="btn btn-outline-danger btn-sm">
                                        Discharge
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-adminlte-card>
    @empty
        <x-adminlte-card>
            <div class="text-center text-muted py-5">No patients currently admitted.</div>
        </x-adminlte-card>
    @endforelse

    @if ($dischargingId)
        <div class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,.5);" wire:click.self="cancelDischarge">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Discharge Patient</h5>
                    </div>
                    <div class="modal-body">
                        <x-input-label for="discharge_notes" value="Discharge notes (optional)" />
                        <textarea wire:model="discharge_notes" id="discharge_notes" rows="3" class="form-control"></textarea>
                        <x-input-error :messages="$errors->get('discharge_notes')" class="mt-1" />
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:click="cancelDischarge" class="btn btn-outline-secondary">Cancel</button>
                        <button type="button" wire:click="discharge" class="btn btn-danger">Confirm Discharge</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
