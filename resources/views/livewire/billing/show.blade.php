<div class="row">
    <div class="col-12 col-lg-8">
        <x-adminlte-card icon="bi bi-receipt"
            title="Invoice {{ $invoice->invoice_number }}">
            <x-slot name="tools">
                <span @class([
                    'badge',
                    'text-bg-success' => $invoice->status === 'paid',
                    'text-bg-warning' => $invoice->status === 'partially_paid',
                    'text-bg-danger' => $invoice->status === 'unpaid',
                ])>
                    {{ str($invoice->status)->headline() }}
                </span>
            </x-slot>

            <p class="text-muted mb-3">{{ $invoice->patient->fullName() }} &middot; {{ $invoice->patient->patient_number }}</p>

            <div class="table-responsive mb-3">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoice->items as $item)
                            <tr>
                                <td>{{ $item->description }}</td>
                                <td class="text-end">{{ $item->quantity }}</td>
                                <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-end">{{ number_format($item->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-top pt-3">
                <div class="d-flex justify-content-between"><span class="text-muted">Subtotal</span><span>{{ number_format($invoice->subtotal, 2) }}</span></div>
                <div class="d-flex justify-content-between"><span class="text-muted">Discount</span><span>-{{ number_format($invoice->discount, 2) }}</span></div>
                <div class="d-flex justify-content-between fw-semibold fs-5"><span>Total</span><span>{{ number_format($invoice->total, 2) }}</span></div>
                <div class="d-flex justify-content-between text-success"><span>Paid</span><span>{{ number_format($invoice->amountPaid(), 2) }}</span></div>
                <div class="d-flex justify-content-between fw-semibold"><span>Balance Due</span><span>{{ number_format($invoice->balanceDue(), 2) }}</span></div>
            </div>

            @if ($invoice->balanceDue() > 0)
                <form wire:submit="recordPayment" class="d-flex align-items-end gap-3 border-top pt-3 mt-3">
                    <div class="flex-grow-1">
                        <x-input-label for="paymentAmount" value="Record cash payment" />
                        <x-text-input type="number" min="0.01" step="0.01" wire:model="paymentAmount" id="paymentAmount" />
                        <x-input-error :messages="$errors->get('paymentAmount')" class="mt-1" />
                    </div>
                    <x-primary-button>Record Payment</x-primary-button>
                </form>
            @endif

            <h5 class="mt-4 mb-2">Payment History</h5>
            <ul class="list-group list-group-flush">
                @forelse ($invoice->payments as $payment)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $payment->paid_at->format('d M Y H:i') }} &middot; {{ ucfirst($payment->method) }}</span>
                        <span class="fw-medium">{{ number_format($payment->amount, 2) }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">No payments recorded yet.</li>
                @endforelse
            </ul>
        </x-adminlte-card>
    </div>
</div>
