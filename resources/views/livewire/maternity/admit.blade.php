<div class="row">
    <div class="col-12 col-lg-8">
        <x-adminlte-card icon="bi bi-heart-pulse-fill"
            title="Start Labour — {{ $visit->patient->fullName() }} ({{ $visit->patient->patient_number }})">
            <form wire:submit="save">
                <div class="row g-3">
                    <div class="col-md-4">
                        <x-input-label for="gravida" value="Gravida" />
                        <x-text-input type="number" min="0" max="20" wire:model="gravida" id="gravida" />
                        <x-input-error :messages="$errors->get('gravida')" class="mt-1" />
                    </div>
                    <div class="col-md-4">
                        <x-input-label for="para" value="Para" />
                        <x-text-input type="number" min="0" max="20" wire:model="para" id="para" />
                        <x-input-error :messages="$errors->get('para')" class="mt-1" />
                    </div>
                    <div class="col-md-4">
                        <x-input-label for="expected_delivery_date" value="Expected delivery date" />
                        <x-text-input type="date" wire:model="expected_delivery_date" id="expected_delivery_date" />
                        <x-input-error :messages="$errors->get('expected_delivery_date')" class="mt-1" />
                    </div>

                    <div class="col-md-6">
                        <x-input-label for="labour_onset_at" value="Labour onset / admission time" />
                        <x-text-input type="datetime-local" wire:model="labour_onset_at" id="labour_onset_at" />
                        <x-input-error :messages="$errors->get('labour_onset_at')" class="mt-1" />
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-end gap-3 mt-4">
                    <a href="{{ route('opd.consultation') }}" wire:navigate class="small">Cancel</a>
                    <x-primary-button>Start Partograph</x-primary-button>
                </div>
            </form>
        </x-adminlte-card>
    </div>
</div>
