<?php

namespace App\Livewire\Pharmacy;

use App\Models\Drug;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Show extends Component
{
    public Prescription $prescription;

    public function mount(Prescription $prescription): void
    {
        $this->prescription = $prescription->load(['patient', 'prescribedBy', 'items.drug', 'items.dispensedBy']);
    }

    public function dispense(int $itemId): void
    {
        $item = PrescriptionItem::where('prescription_id', $this->prescription->id)->findOrFail($itemId);

        if ($item->status === 'dispensed') {
            return;
        }

        $drug = Drug::lockForUpdate()->findOrFail($item->drug_id);

        if ($drug->quantity_on_hand < $item->quantity) {
            $this->addError('stock.'.$itemId, "Only {$drug->quantity_on_hand} {$drug->unit}(s) of {$drug->name} in stock.");

            return;
        }

        DB::transaction(function () use ($item, $drug) {
            $drug->decrement('quantity_on_hand', $item->quantity);

            $drug->stockMovements()->create([
                'type' => 'dispense',
                'quantity_change' => -$item->quantity,
                'reason' => 'Prescription #'.$this->prescription->id,
                'recorded_by' => Auth::id(),
            ]);

            $item->update([
                'status' => 'dispensed',
                'dispensed_by' => Auth::id(),
                'dispensed_at' => now(),
            ]);
        });

        $this->prescription->refreshStatus();
        $this->prescription->refresh()->load(['items.drug', 'items.dispensedBy']);
    }

    public function render()
    {
        return view('livewire.pharmacy.show');
    }
}
