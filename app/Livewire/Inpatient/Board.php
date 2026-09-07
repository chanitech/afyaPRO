<?php

namespace App\Livewire\Inpatient;

use App\Models\Admission;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Board extends Component
{
    public ?int $dischargingId = null;

    #[Validate('nullable|string|max:2000')]
    public string $discharge_notes = '';

    public function startDischarge(int $admissionId): void
    {
        $this->dischargingId = $admissionId;
        $this->discharge_notes = '';
    }

    public function cancelDischarge(): void
    {
        $this->dischargingId = null;
    }

    public function discharge(): void
    {
        $this->validate();

        $admission = Admission::where('facility_id', Auth::user()->facility_id)
            ->where('status', 'admitted')
            ->findOrFail($this->dischargingId);

        $admission->update([
            'status' => 'discharged',
            'discharged_at' => now(),
            'discharge_notes' => $this->discharge_notes ?: null,
        ]);

        $admission->bed->update(['status' => 'available']);

        $this->dischargingId = null;
        session()->flash('status', $admission->patient->fullName().' discharged.');
    }

    public function render()
    {
        $admissions = Admission::with(['patient', 'ward', 'bed', 'admittingDoctor'])
            ->where('facility_id', Auth::user()->facility_id)
            ->where('status', 'admitted')
            ->orderBy('admitted_at')
            ->get()
            ->groupBy('ward.name');

        return view('livewire.inpatient.board', compact('admissions'));
    }
}
