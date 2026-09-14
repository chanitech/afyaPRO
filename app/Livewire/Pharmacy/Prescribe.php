<?php

namespace App\Livewire\Pharmacy;

use App\Models\Drug;
use App\Models\OpdVisit;
use App\Models\Prescription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Prescribe extends Component
{
    public OpdVisit $visit;

    /** @var array<int, array{drug_id: string, quantity: int, dosage_instructions: string}> */
    public array $items = [
        ['drug_id' => '', 'quantity' => 1, 'dosage_instructions' => ''],
    ];

    public function mount(OpdVisit $visit): void
    {
        $this->visit = $visit->loadMissing('patient');
    }

    public function addItem(): void
    {
        $this->items[] = ['drug_id' => '', 'quantity' => 1, 'dosage_instructions' => ''];
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function save()
    {
        $this->validate([
            'items.*.drug_id' => 'required|exists:drugs,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.dosage_instructions' => 'nullable|string|max:255',
        ]);

        $drugs = Drug::whereKey(collect($this->items)->pluck('drug_id'))->get()->keyBy('id');

        DB::transaction(function () use ($drugs) {
            $prescription = Prescription::create([
                'facility_id' => $this->visit->facility_id,
                'patient_id' => $this->visit->patient_id,
                'opd_visit_id' => $this->visit->id,
                'prescribed_by' => Auth::id(),
                'status' => 'prescribed',
                'prescribed_at' => now(),
            ]);

            foreach ($this->items as $item) {
                $drug = $drugs->get($item['drug_id']);

                $prescription->items()->create([
                    'drug_id' => $drug->id,
                    'quantity' => $item['quantity'],
                    'dosage_instructions' => $item['dosage_instructions'] ?: null,
                    'unit_price' => $drug->unit_price,
                    'status' => 'pending',
                ]);
            }
        });

        session()->flash('status', 'Prescription created for '.$this->visit->patient->fullName().'.');

        return $this->redirect(route('opd.consultation'), navigate: true);
    }

    public function render()
    {
        $drugs = Drug::where('facility_id', $this->visit->facility_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('livewire.pharmacy.prescribe', compact('drugs'));
    }
}
