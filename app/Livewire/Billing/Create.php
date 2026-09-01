<?php

namespace App\Livewire\Billing;

use App\Models\Invoice;
use App\Models\OpdVisit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    public OpdVisit $visit;

    /** @var array<int, array{description: string, quantity: int, unit_price: float}> */
    public array $items = [
        ['description' => 'Consultation fee', 'quantity' => 1, 'unit_price' => 0],
    ];

    #[Validate('numeric|min:0')]
    public string $discount = '0';

    #[Validate('numeric|min:0')]
    public string $amountReceived = '0';

    public function mount(OpdVisit $visit): void
    {
        $this->visit = $visit->loadMissing('patient');

        if ($this->visit->invoice) {
            $this->redirect(route('billing.show', ['invoice' => $this->visit->invoice->id]), navigate: true);
        }
    }

    public function addItem(): void
    {
        $this->items[] = ['description' => '', 'quantity' => 1, 'unit_price' => 0];
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function getSubtotalProperty(): float
    {
        return collect($this->items)->sum(fn ($item) => (float) ($item['quantity'] ?? 0) * (float) ($item['unit_price'] ?? 0));
    }

    public function getTotalProperty(): float
    {
        return max(0, $this->subtotal - (float) $this->discount);
    }

    public function save()
    {
        $this->validate([
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'discount' => 'numeric|min:0',
            'amountReceived' => 'numeric|min:0',
        ]);

        $invoice = DB::transaction(function () {
            $invoice = Invoice::create([
                'facility_id' => $this->visit->facility_id,
                'patient_id' => $this->visit->patient_id,
                'opd_visit_id' => $this->visit->id,
                'subtotal' => $this->subtotal,
                'discount' => $this->discount,
                'total' => $this->total,
                'payment_method' => 'cash',
                'created_by' => Auth::id(),
            ]);

            foreach ($this->items as $item) {
                $invoice->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'amount' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            if ((float) $this->amountReceived > 0) {
                $invoice->payments()->create([
                    'amount' => min((float) $this->amountReceived, $this->total),
                    'method' => 'cash',
                    'received_by' => Auth::id(),
                    'paid_at' => now(),
                ]);
            }

            return $invoice;
        });

        return $this->redirect(route('billing.show', ['invoice' => $invoice->id]), navigate: true);
    }

    public function render()
    {
        return view('livewire.billing.create');
    }
}
