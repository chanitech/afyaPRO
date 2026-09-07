<div>
    @if (session('status'))
        <x-adminlte-alert theme="success" class="mb-3">
            {{ session('status') }}
        </x-adminlte-alert>
    @endif

    <div class="row g-3">
        <div class="col-12 col-lg-4">
            <x-adminlte-card title="Add Ward" icon="bi bi-plus-circle">
                <form wire:submit="addWard">
                    <div class="mb-3">
                        <x-input-label for="name" value="Name" />
                        <x-text-input wire:model="name" id="name" placeholder="General Medical Ward" />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>
                    <div class="mb-3">
                        <x-input-label for="code" value="Code" />
                        <x-text-input wire:model="code" id="code" placeholder="GMW" />
                        <x-input-error :messages="$errors->get('code')" class="mt-1" />
                    </div>
                    <div class="mb-3">
                        <x-input-label for="ward_type" value="Ward type (optional)" />
                        <x-text-input wire:model="ward_type" id="ward_type" placeholder="general, icu, maternity..." />
                    </div>
                    <div class="d-flex justify-content-end">
                        <x-primary-button>Add Ward</x-primary-button>
                    </div>
                </form>
            </x-adminlte-card>
        </div>

        <div class="col-12 col-lg-8">
            @forelse ($wards as $ward)
                <x-adminlte-card icon="bi bi-hospital" class="mb-3"
                    title="{{ $ward->name }} ({{ $ward->code }})">
                    <x-slot name="tools">
                        <span class="badge text-bg-secondary">
                            {{ $ward->beds->where('status', 'available')->count() }} / {{ $ward->beds->count() }} available
                        </span>
                    </x-slot>

                    <div class="row g-2 mb-3">
                        @forelse ($ward->beds as $bed)
                            <div class="col-auto">
                                <span @class([
                                    'badge',
                                    'text-bg-success' => $bed->status === 'available',
                                    'text-bg-danger' => $bed->status === 'occupied',
                                    'text-bg-secondary' => $bed->status === 'maintenance',
                                ])>
                                    Bed {{ $bed->bed_number }}
                                </span>
                            </div>
                        @empty
                            <div class="col-12 text-muted small">No beds in this ward yet.</div>
                        @endforelse
                    </div>

                    <form wire:submit="addBed({{ $ward->id }})" class="d-flex gap-2">
                        <x-text-input wire:model="newBedNumber.{{ $ward->id }}" placeholder="Bed number" class="w-auto" style="max-width: 10rem;" />
                        <button type="submit" class="btn btn-outline-primary btn-sm">Add Bed</button>
                    </form>
                    <x-input-error :messages="$errors->get('newBedNumber.'.$ward->id)" class="mt-1" />
                </x-adminlte-card>
            @empty
                <x-adminlte-card>
                    <div class="text-center text-muted py-5">No wards set up yet. Add one to get started.</div>
                </x-adminlte-card>
            @endforelse
        </div>
    </div>
</div>
