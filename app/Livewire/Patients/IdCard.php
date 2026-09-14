<?php

namespace App\Livewire\Patients;

use App\Models\Patient;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.print')]
class IdCard extends Component
{
    public Patient $patient;

    public function mount(Patient $patient): void
    {
        $this->patient = $patient->loadMissing('facility');
    }

    public function render()
    {
        return view('livewire.patients.id-card');
    }
}
