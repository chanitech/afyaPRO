<div>
    @if (session('status'))
        <x-adminlte-alert theme="success" class="mb-3">
            {{ session('status') }}
        </x-adminlte-alert>
    @endif

    <div class="row g-3">
        <div class="col-12 col-lg-4">
            <x-adminlte-card title="Add Drug" icon="bi bi-plus-circle">
                <form wire:submit="addDrug">
                    <div class="mb-3">
                        <x-input-label for="name" value="Name" />
                        <x-text-input wire:model="name" id="name" placeholder="Paracetamol 500mg" />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>
                    <div class="mb-3">
                        <x-input-label for="generic_name" value="Generic name (optional)" />
                        <x-text-input wire:model="generic_name" id="generic_name" />
                    </div>
                    <div class="mb-3">
                        <x-input-label for="form" value="Form (optional)" />
                        <x-text-input wire:model="form" id="form" placeholder="tablet, syrup, injection..." />
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <x-input-label for="unit" value="Unit" />
                            <x-text-input wire:model="unit" id="unit" placeholder="tablet" />
                            <x-input-error :messages="$errors->get('unit')" class="mt-1" />
                        </div>
                        <div class="col-6">
                            <x-input-label for="unit_price" value="Unit price" />
                            <x-text-input type="number" min="0" step="0.01" wire:model="unit_price" id="unit_price" />
                            <x-input-error :messages="$errors->get('unit_price')" class="mt-1" />
                        </div>
                    </div>
                    <div class="mb-3">
                        <x-input-label for="reorder_level" value="Reorder level" />
                        <x-text-input type="number" min="0" wire:model="reorder_level" id="reorder_level" />
                    </div>
                    <div class="d-flex justify-content-end">
                        <x-primary-button>Add Drug</x-primary-button>
                    </div>
                </form>
            </x-adminlte-card>
        </div>

        <div class="col-12 col-lg-8">
            <x-adminlte-card title="Drug Catalog" icon="bi bi-boxes">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Drug</th>
                                <th>Unit Price</th>
                                <th>In Stock</th>
                                <th>Receive Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($drugs as $drug)
                                <tr>
                                    <td>
                                        <div class="fw-medium">{{ $drug->name }}</div>
                                        @if ($drug->generic_name)
                                            <div class="small text-muted">{{ $drug->generic_name }}</div>
                                        @endif
                                    </td>
                                    <td>{{ number_format($drug->unit_price, 2) }}</td>
                                    <td>
                                        <span @class(['badge', 'text-bg-danger' => $drug->isLowStock(), 'text-bg-success' => ! $drug->isLowStock()])>
                                            {{ $drug->quantity_on_hand }} {{ $drug->unit }}
                                        </span>
                                        @if ($drug->isLowStock())
                                            <div class="small text-danger">Low stock (reorder at {{ $drug->reorder_level }})</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <x-text-input type="number" min="1" wire:model="receiveQty.{{ $drug->id }}" class="w-auto" style="max-width: 6rem;" />
                                            <button type="button" wire:click="receiveStock({{ $drug->id }})" class="btn btn-outline-primary btn-sm">Receive</button>
                                        </div>
                                        <x-input-error :messages="$errors->get('receiveQty.'.$drug->id)" class="mt-1" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No drugs in the catalog yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $drugs->links() }}
            </x-adminlte-card>
        </div>
    </div>
</div>
