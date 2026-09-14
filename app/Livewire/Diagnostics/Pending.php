<?php

namespace App\Livewire\Diagnostics;

use App\Models\DiagnosticOrder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Pending extends Component
{
    use WithPagination;

    public function render()
    {
        $orders = DiagnosticOrder::with(['patient', 'orderedBy', 'items.test'])
            ->where('facility_id', Auth::user()->facility_id)
            ->whereIn('status', ['ordered', 'in_progress'])
            ->orderBy('ordered_at')
            ->paginate(15);

        return view('livewire.diagnostics.pending', compact('orders'));
    }
}
