<?php

namespace App\Livewire\Billing;

use App\Models\OpdVisit;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Pending extends Component
{
    use WithPagination;

    public function render()
    {
        $visits = OpdVisit::with(['patient', 'doctor'])
            ->where('facility_id', Auth::user()->facility_id)
            ->where('status', 'completed')
            ->whereDoesntHave('invoice')
            ->orderByDesc('ended_at')
            ->paginate(15);

        return view('livewire.billing.pending', compact('visits'));
    }
}
