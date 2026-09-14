<div class="row">
    <div class="col-12 col-lg-8">
        <x-adminlte-card icon="bi bi-hospital"
            title="Admit — {{ $visit->patient->fullName() }} ({{ $visit->patient->patient_number }})">
            <form wire:submit="save">
                <div class="mb-3">
                    <x-input-label for="bed_id" value="Ward &amp; bed" />
                    <select wire:model="bed_id" id="bed_id" class="form-select">
                        <option value="">Select a bed...</option>
                        @foreach ($wards as $ward)
                            @if ($ward->beds->isNotEmpty())
                                <optgroup label="{{ $ward->name }}">
                                    @foreach ($ward->beds as $bed)
                                        <option value="{{ $bed->id }}">Bed {{ $bed->bed_number }}</option>
                                    @endforeach
                                </optgroup>
                            @endif
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('bed_id')" class="mt-1" />
                    @if ($wards->every(fn ($ward) => $ward->beds->isEmpty()))
                        <p class="small text-muted mt-1">
                            No available beds. <a href="{{ route('inpatient.wards') }}" wire:navigate>Manage wards &amp; beds</a>.
                        </p>
                    @endif
                </div>

                <div class="mb-3">
                    <x-input-label for="admission_reason" value="Reason for admission" />
                    <textarea wire:model="admission_reason" id="admission_reason" rows="3" class="form-control"></textarea>
                    <x-input-error :messages="$errors->get('admission_reason')" class="mt-1" />
                </div>

                <div class="d-flex align-items-center justify-content-end gap-3 mt-4">
                    <a href="{{ route('opd.consultation') }}" wire:navigate class="small">Cancel</a>
                    <x-primary-button>Admit Patient</x-primary-button>
                </div>
            </form>
        </x-adminlte-card>
    </div>
</div>
