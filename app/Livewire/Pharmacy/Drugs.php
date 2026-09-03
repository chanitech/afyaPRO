<?php

namespace App\Livewire\Pharmacy;

use App\Models\Drug;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class Drugs extends Component
{
    use WithPagination;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('nullable|string|max:255')]
    public string $generic_name = '';

    #[Validate('nullable|string|max:100')]
    public string $form = '';

    #[Validate('required|string|max:50')]
    public string $unit = '';

    #[Validate('required|numeric|min:0')]
    public string $unit_price = '';

    #[Validate('required|integer|min:0')]
    public string $reorder_level = '0';

    /** @var array<int, string> */
    public array $receiveQty = [];

    public function addDrug(): void
    {
        $this->validate();

        Drug::create([
            'facility_id' => Auth::user()->facility_id,
            'name' => $this->name,
            'generic_name' => $this->generic_name ?: null,
            'form' => $this->form ?: null,
            'unit' => $this->unit,
            'unit_price' => $this->unit_price,
            'reorder_level' => $this->reorder_level,
            'quantity_on_hand' => 0,
            'is_active' => true,
        ]);

        $this->reset(['name', 'generic_name', 'form', 'unit', 'unit_price', 'reorder_level']);
        session()->flash('status', 'Drug added to catalog.');
    }

    public function receiveStock(int $drugId): void
    {
        $this->validate([
            "receiveQty.{$drugId}" => 'required|integer|min:1',
        ]);

        $drug = Drug::where('facility_id', Auth::user()->facility_id)->findOrFail($drugId);
        $qty = (int) $this->receiveQty[$drugId];

        $drug->increment('quantity_on_hand', $qty);

        $drug->stockMovements()->create([
            'type' => 'receipt',
            'quantity_change' => $qty,
            'reason' => 'Stock received',
            'recorded_by' => Auth::id(),
        ]);

        unset($this->receiveQty[$drugId]);
        session()->flash('status', "Received {$qty} {$drug->unit}(s) of {$drug->name}.");
    }

    public function render()
    {
        $drugs = Drug::where('facility_id', Auth::user()->facility_id)
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.pharmacy.drugs', compact('drugs'));
    }
}
