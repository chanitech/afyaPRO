<?php

namespace App\Livewire\Insurance;

use App\Models\InsuranceClaim;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $status = '';

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $claims = InsuranceClaim::with(['patient', 'invoice'])
            ->where('facility_id', Auth::user()->facility_id)
            ->when($this->status, fn ($query) => $query->where('status', $this->status))
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('livewire.insurance.index', compact('claims'));
    }
}
