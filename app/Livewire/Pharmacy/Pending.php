<?php

namespace App\Livewire\Pharmacy;

use App\Models\Prescription;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Pending extends Component
{
    use WithPagination;

    public function render()
    {
        $prescriptions = Prescription::with(['patient', 'prescribedBy', 'items.drug'])
            ->where('facility_id', Auth::user()->facility_id)
            ->whereIn('status', ['prescribed', 'partially_dispensed'])
            ->orderBy('prescribed_at')
            ->paginate(15);

        return view('livewire.pharmacy.pending', compact('prescriptions'));
    }
}
