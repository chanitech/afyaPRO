<?php

namespace App\Livewire\Maternity;

use App\Models\Delivery;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Board extends Component
{
    public function render()
    {
        $inLabour = Delivery::with(['patient', 'attending', 'observations'])
            ->where('facility_id', Auth::user()->facility_id)
            ->where('status', 'in_labour')
            ->orderBy('labour_onset_at')
            ->get();

        $recentlyDelivered = Delivery::with('patient')
            ->where('facility_id', Auth::user()->facility_id)
            ->where('status', 'delivered')
            ->orderByDesc('delivered_at')
            ->limit(10)
            ->get();

        return view('livewire.maternity.board', compact('inLabour', 'recentlyDelivered'));
    }
}
