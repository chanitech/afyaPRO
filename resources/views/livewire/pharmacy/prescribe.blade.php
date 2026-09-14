<div class="row">
    <div class="col-12 col-lg-9">
        <x-adminlte-card icon="bi bi-capsule"
            title="Prescribe — {{ $visit->patient->fullName() }} ({{ $visit->patient->patient_number }})">
            <form wire:submit="save">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Drug</th>
                                <th style="width: 7rem;">Qty</th>
                                <th>Dosage instructions</th>
                                <th style="width: 2.5rem;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $index => $item)
                                <tr>
                                    <td>
                                        <select wire:model="items.{{ $index }}.drug_id" class="form-select">
                                            <option value="">Select drug...</option>
                                            @foreach ($drugs as $drug)
                                                <option value="{{ $drug->id }}">{{ $drug->name }} ({{ $drug->quantity_on_hand }} {{ $drug->unit }} in stock)</option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('items.'.$index.'.drug_id')" class="mt-1" />
                                    </td>
                                    <td>
                                        <x-text-input type="number" min="1" wire:model="items.{{ $index }}.quantity" />
                                    </td>
                                    <td>
                                        <x-text-input wire:model="items.{{ $index }}.dosage_instructions" placeholder="e.g. 1 tablet twice daily for 5 days" />
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

                <button type="button" wire:click="addItem" class="btn btn-link btn-sm px-0">
                    <i class="bi bi-plus-lg"></i> Add drug
                </button>

                <div class="d-flex align-items-center justify-content-end gap-3 mt-4">
                    <a href="{{ route('opd.consultation') }}" wire:navigate class="small">Cancel</a>
                    <x-primary-button>Save Prescription</x-primary-button>
                </div>
            </form>
        </x-adminlte-card>
    </div>
</div>
