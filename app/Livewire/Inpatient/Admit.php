<?php

namespace App\Livewire\Inpatient;

use App\Models\Admission;
use App\Models\Bed;
use App\Models\OpdVisit;
use App\Models\Ward;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Admit extends Component
{
    public OpdVisit $visit;

    #[Validate('required|exists:beds,id')]
    public string $bed_id = '';

    #[Validate('nullable|string|max:2000')]
    public string $admission_reason = '';

    public function mount(OpdVisit $visit): void
    {
        $this->visit = $visit->loadMissing('patient');
    }

    public function save()
    {
        $this->validate();

        $bed = Bed::lockForUpdate()->findOrFail($this->bed_id);

        if (! $bed->isAvailable()) {
            $this->addError('bed_id', 'That bed is no longer available. Please pick another.');

            return;
        }

        DB::transaction(function () use ($bed) {
            Admission::create([
                'facility_id' => $this->visit->facility_id,
                'patient_id' => $this->visit->patient_id,
                'ward_id' => $bed->ward_id,
                'bed_id' => $bed->id,
                'opd_visit_id' => $this->visit->id,
                'admitting_doctor_id' => Auth::id(),
                'admission_reason' => $this->admission_reason ?: null,
                'status' => 'admitted',
                'admitted_at' => now(),
            ]);

            $bed->update(['status' => 'occupied']);
        });

        session()->flash('status', $this->visit->patient->fullName().' admitted to the ward.');

        return $this->redirect(route('opd.consultation'), navigate: true);
    }

    public function render()
    {
        $wards = Ward::with(['beds' => fn ($q) => $q->where('status', 'available')])
            ->where('facility_id', $this->visit->facility_id)
            ->orderBy('name')
            ->get();

        return view('livewire.inpatient.admit', compact('wards'));
    }
}
