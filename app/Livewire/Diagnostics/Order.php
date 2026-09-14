<?php

namespace App\Livewire\Diagnostics;

use App\Models\DiagnosticOrder;
use App\Models\DiagnosticTest;
use App\Models\OpdVisit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Order extends Component
{
    public OpdVisit $visit;

    /** @var array<int, bool> */
    public array $selected = [];

    public function mount(OpdVisit $visit): void
    {
        $this->visit = $visit->loadMissing('patient');
    }

    public function save()
    {
        $testIds = collect($this->selected)->filter()->keys()->map(fn ($id) => (int) $id);

        if ($testIds->isEmpty()) {
            $this->addError('selected', 'Select at least one test to order.');

            return;
        }

        $tests = DiagnosticTest::whereKey($testIds)->get();

        DB::transaction(function () use ($tests) {
            $order = DiagnosticOrder::create([
                'facility_id' => $this->visit->facility_id,
                'patient_id' => $this->visit->patient_id,
                'opd_visit_id' => $this->visit->id,
                'ordered_by' => Auth::id(),
                'status' => 'ordered',
                'ordered_at' => now(),
            ]);

            foreach ($tests as $test) {
                $order->items()->create([
                    'diagnostic_test_id' => $test->id,
                    'price' => $test->price,
                    'status' => 'pending',
                ]);
            }
        });

        session()->flash('status', 'Tests ordered for '.$this->visit->patient->fullName().'.');

        return $this->redirect(route('opd.consultation'), navigate: true);
    }

    public function render()
    {
        $tests = DiagnosticTest::where('facility_id', $this->visit->facility_id)
            ->where('is_active', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        return view('livewire.diagnostics.order', compact('tests'));
    }
}
