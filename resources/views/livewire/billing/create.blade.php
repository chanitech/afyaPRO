<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow-sm rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-1">Create Invoice</h3>
        <p class="text-sm text-gray-500 mb-4">{{ $visit->patient->fullName() }} &middot; {{ $visit->patient->patient_number }}</p>

        <form wire:submit="save" class="space-y-4">
            <table class="min-w-full">
                <thead>
                    <tr class="text-left text-xs font-medium text-gray-500 uppercase">
                        <th class="pb-2">Description</th>
                        <th class="pb-2 w-20">Qty</th>
                        <th class="pb-2 w-32">Unit Price</th>
                        <th class="pb-2 w-32 text-right">Amount</th>
                        <th class="pb-2 w-10"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $index => $item)
                        <tr>
                            <td class="py-1 pr-2">
                                <x-text-input wire:model="items.{{ $index }}.description" class="w-full" />
                            </td>
                            <td class="py-1 pr-2">
                                <x-text-input type="number" min="1" wire:model="items.{{ $index }}.quantity" class="w-full" />
                            </td>
                            <td class="py-1 pr-2">
                                <x-text-input type="number" min="0" step="0.01" wire:model="items.{{ $index }}.unit_price" class="w-full" />
                            </td>
                            <td class="py-1 text-right text-sm text-gray-700">
                                {{ number_format(($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0), 2) }}
                            </td>
                            <td class="py-1 text-right">
                                @if (count($items) > 1)
                                    <button type="button" wire:click="removeItem({{ $index }})" class="text-red-500 hover:text-red-700">&times;</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <x-input-error :messages="$errors->get('items.*.description')" class="mt-1" />

            <button type="button" wire:click="addItem" class="text-sm text-indigo-600 hover:underline">+ Add line item</button>

            <div class="border-t pt-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Subtotal</span>
                    <span class="font-medium">{{ number_format($this->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-600">Discount</span>
                    <x-text-input type="number" min="0" step="0.01" wire:model.live="discount" class="w-32 text-right" />
                </div>
                <div class="flex justify-between text-base font-semibold">
                    <span>Total Due</span>
                    <span>{{ number_format($this->total, 2) }}</span>
                </div>
                <div class="flex justify-between items-center text-sm pt-2">
                    <span class="text-gray-600">Cash received now</span>
                    <x-text-input type="number" min="0" step="0.01" wire:model="amountReceived" class="w-32 text-right" />
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <x-primary-button>Save Invoice</x-primary-button>
            </div>
        </form>
    </div>
</div>
