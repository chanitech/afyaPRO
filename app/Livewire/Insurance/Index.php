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

    public string $insurer = '';

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingInsurer(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $claims = InsuranceClaim::with(['patient', 'invoice'])
            ->where('facility_id', Auth::user()->facility_id)
            ->when($this->status, fn ($query) => $query->where('status', $this->status))
            ->when($this->insurer, fn ($query) => $query->where('insurer', $this->insurer))
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('livewire.insurance.index', [
            'claims' => $claims,
            'insurers' => InsuranceClaim::INSURERS,
        ]);
    }
}
