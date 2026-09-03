<div class="row">
    <div class="col-12 col-lg-8">
        <x-adminlte-card icon="bi bi-clipboard2-pulse"
            title="Order Tests — {{ $visit->patient->fullName() }} ({{ $visit->patient->patient_number }})">
            @error('selected')
                <x-adminlte-alert theme="danger" class="mb-3">{{ $message }}</x-adminlte-alert>
            @enderror

            <form wire:submit="save">
                @foreach ($tests as $category => $categoryTests)
                    <h6 class="text-uppercase text-muted small mt-3 mb-2">{{ $category === 'lab' ? 'Laboratory' : 'Radiology' }}</h6>
                    <div class="row g-2 mb-3">
                        @foreach ($categoryTests as $test)
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" wire:model="selected.{{ $test->id }}" id="test-{{ $test->id }}" class="form-check-input">
                                    <label for="test-{{ $test->id }}" class="form-check-label d-flex justify-content-between">
                                        <span>{{ $test->name }}</span>
                                        <span class="text-muted small">{{ number_format($test->price, 2) }}</span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach

                <div class="d-flex align-items-center justify-content-end gap-3 mt-4">
                    <a href="{{ route('opd.consultation') }}" wire:navigate class="small">Cancel</a>
                    <x-primary-button>Order Tests</x-primary-button>
                </div>
            </form>
        </x-adminlte-card>
    </div>
</div>
