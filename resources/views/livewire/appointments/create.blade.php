<div class="row">
    <div class="col-12 col-lg-8">
        <x-adminlte-card title="Book Appointment" icon="bi bi-calendar2-plus">
            <div class="mb-4">
                <x-input-label value="Patient" />

                @if ($this->patient)
                    <div class="d-flex align-items-center justify-content-between border rounded p-3 bg-body-tertiary">
                        <div>
                            <div class="fw-medium">{{ $this->patient->fullName() }}</div>
                            <div class="small text-muted">{{ $this->patient->patient_number }} &middot; {{ $this->patient->phone }}</div>
                        </div>
                        <button type="button" wire:click="clearPatient" class="btn btn-link btn-sm">Change</button>
                    </div>
                @else
                    <div class="position-relative">
                        <x-text-input wire:model.live.debounce.300ms="patientSearch" placeholder="Search by name, patient number or phone..." />

                        @if ($this->searchResults->isNotEmpty())
                            <div class="list-group position-absolute w-100 shadow-sm" style="z-index: 10;">
                                @foreach ($this->searchResults as $result)
                                    <button type="button" wire:click="selectPatient({{ $result->id }})"
                                        class="list-group-item list-group-item-action">
                                        <span class="fw-medium">{{ $result->fullName() }}</span>
                                        <span class="text-muted">— {{ $result->patient_number }}</span>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <p class="small text-muted mt-2 mb-0">
                        Patient not found? <a href="{{ route('patients.create') }}" wire:navigate>Register a new patient</a>.
                    </p>
                @endif
                <x-input-error :messages="$errors->get('patientId')" class="mt-1" />
            </div>

            <form wire:submit="save">
                <div class="row g-3">
                    <div class="col-md-6">
                        <x-input-label for="department_id" value="Department" />
                        <select wire:model="department_id" id="department_id" class="form-select">
                            <option value="">Select...</option>
                            @foreach ($this->departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('department_id')" class="mt-1" />
                    </div>
                    <div class="col-md-6">
                        <x-input-label for="doctor_id" value="Doctor (optional)" />
                        <select wire:model="doctor_id" id="doctor_id" class="form-select">
                            <option value="">Any available doctor</option>
                            @foreach ($this->doctors as $doctor)
                                <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <x-input-label for="appointment_date" value="Date" />
                        <x-text-input type="date" wire:model="appointment_date" id="appointment_date" />
                        <x-input-error :messages="$errors->get('appointment_date')" class="mt-1" />
                    </div>
                    <div class="col-md-6">
                        <x-input-label for="appointment_time" value="Time" />
                        <x-text-input type="time" wire:model="appointment_time" id="appointment_time" />
                        <x-input-error :messages="$errors->get('appointment_time')" class="mt-1" />
                    </div>

                    <div class="col-12">
                        <x-input-label for="reason" value="Reason for visit (optional)" />
                        <x-text-input wire:model="reason" id="reason" />
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-end gap-3 mt-4">
                    <a href="{{ route('appointments.index') }}" wire:navigate class="small">Cancel</a>
                    <x-primary-button>Book Appointment</x-primary-button>
                </div>
            </form>
        </x-adminlte-card>
    </div>
</div>
