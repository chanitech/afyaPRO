<div class="row">
    <div class="col-12 col-lg-9">
        <x-adminlte-card title="Create Invoice" icon="bi bi-receipt-cutoff">
            <p class="text-muted mb-3">{{ $visit->patient->fullName() }} &middot; {{ $visit->patient->patient_number }}</p>

            <form wire:submit="save">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th class="w-auto" style="width: 6rem;">Qty</th>
                                <th style="width: 9rem;">Unit Price</th>
                                <th class="text-end" style="width: 9rem;">Amount</th>
                                <th style="width: 2.5rem;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $index => $item)
                                <tr>
                                    <td>
                                        <x-text-input wire:model="items.{{ $index }}.description" />
                                    </td>
                                    <td>
                                        <x-text-input type="number" min="1" wire:model="items.{{ $index }}.quantity" />
                                    </td>
                                    <td>
                                        <x-text-input type="number" min="0" step="0.01" wire:model="items.{{ $index }}.unit_price" />
                                    </td>
                                    <td class="text-end">
                                        {{ number_format(($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0), 2) }}
                                    </td>
                                    <td class="text-end">
                                        @if (count($items) > 1)
                                            <button type="button" wire:click="removeItem({{ $index }})" class="btn btn-link btn-sm text-danger">&times;</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <x-input-error :messages="$errors->get('items.*.description')" class="mt-1" />

                <button type="button" wire:click="addItem" class="btn btn-link btn-sm px-0">
                    <i class="bi bi-plus-lg"></i> Add line item
                </button>

                <div class="border-top pt-3 mt-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal</span>
                        <span class="fw-medium">{{ number_format($this->subtotal, 2) }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted">Discount</span>
                        <x-text-input type="number" min="0" step="0.01" wire:model.live="discount" class="w-auto text-end" style="max-width: 8rem;" />
                    </div>
                    <div class="d-flex justify-content-between fs-5 fw-semibold">
                        <span>Total Due</span>
                        <span>{{ number_format($this->total, 2) }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mt-3">
                        <span class="text-muted">Cash received now</span>
                        <x-text-input type="number" min="0" step="0.01" wire:model="amountReceived" class="w-auto text-end" style="max-width: 8rem;" />
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <x-primary-button>Save Invoice</x-primary-button>
                </div>
            </form>
        </x-adminlte-card>
    </div>
</div>
