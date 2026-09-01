<div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow-sm rounded-lg p-6">
        <div class="flex items-center justify-between mb-1">
            <h3 class="text-lg font-medium text-gray-900">Invoice {{ $invoice->invoice_number }}</h3>
            <span @class([
                'px-2 py-1 rounded-full text-xs font-medium',
                'bg-green-100 text-green-800' => $invoice->status === 'paid',
                'bg-yellow-100 text-yellow-800' => $invoice->status === 'partially_paid',
                'bg-red-100 text-red-800' => $invoice->status === 'unpaid',
            ])>
                {{ str($invoice->status)->headline() }}
            </span>
        </div>
        <p class="text-sm text-gray-500 mb-4">{{ $invoice->patient->fullName() }} &middot; {{ $invoice->patient->patient_number }}</p>

        <table class="min-w-full mb-4">
            <thead>
                <tr class="text-left text-xs font-medium text-gray-500 uppercase">
                    <th class="pb-2">Description</th>
                    <th class="pb-2 w-16 text-right">Qty</th>
                    <th class="pb-2 w-28 text-right">Unit Price</th>
                    <th class="pb-2 w-28 text-right">Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($invoice->items as $item)
                    <tr>
                        <td class="py-2 text-sm">{{ $item->description }}</td>
                        <td class="py-2 text-sm text-right">{{ $item->quantity }}</td>
                        <td class="py-2 text-sm text-right">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="py-2 text-sm text-right">{{ number_format($item->amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="border-t pt-4 space-y-1 text-sm">
            <div class="flex justify-between"><span class="text-gray-600">Subtotal</span><span>{{ number_format($invoice->subtotal, 2) }}</span></div>
            <div class="flex justify-between"><span class="text-gray-600">Discount</span><span>-{{ number_format($invoice->discount, 2) }}</span></div>
            <div class="flex justify-between font-semibold text-base"><span>Total</span><span>{{ number_format($invoice->total, 2) }}</span></div>
            <div class="flex justify-between text-green-700"><span>Paid</span><span>{{ number_format($invoice->amountPaid(), 2) }}</span></div>
            <div class="flex justify-between font-semibold"><span>Balance Due</span><span>{{ number_format($invoice->balanceDue(), 2) }}</span></div>
        </div>

        @if ($invoice->balanceDue() > 0)
            <form wire:submit="recordPayment" class="mt-6 flex items-end gap-3 border-t pt-4">
                <div class="flex-1">
                    <x-input-label for="paymentAmount" value="Record cash payment" />
                    <x-text-input type="number" min="0.01" step="0.01" wire:model="paymentAmount" id="paymentAmount" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('paymentAmount')" class="mt-2" />
                </div>
                <x-primary-button>Record Payment</x-primary-button>
            </form>
        @endif

        <h4 class="font-medium text-gray-900 mt-6 mb-2">Payment History</h4>
        <ul class="divide-y divide-gray-100">
            @forelse ($invoice->payments as $payment)
                <li class="py-2 text-sm flex justify-between">
                    <span>{{ $payment->paid_at->format('d M Y H:i') }} &middot; {{ ucfirst($payment->method) }}</span>
                    <span class="font-medium">{{ number_format($payment->amount, 2) }}</span>
                </li>
            @empty
                <li class="py-2 text-sm text-gray-500">No payments recorded yet.</li>
            @endforelse
        </ul>
    </div>
</div>
