<?php

namespace App\Livewire\Billing;

use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Show extends Component
{
    public Invoice $invoice;

    #[Validate('numeric|min:0.01')]
    public string $paymentAmount = '';

    public function mount(Invoice $invoice): void
    {
        $this->invoice = $invoice->load(['patient', 'items', 'payments', 'claim']);
    }

    public function recordPayment(): void
    {
        $this->validate();

        $balance = $this->invoice->balanceDue();

        if ((float) $this->paymentAmount > $balance) {
            $this->addError('paymentAmount', 'Amount exceeds the outstanding balance of '.number_format($balance, 2).'.');

            return;
        }

        $this->invoice->payments()->create([
            'amount' => $this->paymentAmount,
            'method' => 'cash',
            'received_by' => Auth::id(),
            'paid_at' => now(),
        ]);

        $this->invoice->refresh();
        $this->paymentAmount = '';
    }

    public function render()
    {
        return view('livewire.billing.show');
    }
}
