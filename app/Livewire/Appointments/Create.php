<?php

namespace App\Livewire\Appointments;

use App\Models\Appointment;
use App\Models\Department;
use App\Models\Patient;
use App\Models\User;
use App\Notifications\AppointmentScheduled;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    public ?int $patientId = null;

    public string $patientSearch = '';

    #[Validate('required|exists:departments,id')]
    public string $department_id = '';

    #[Validate('nullable|exists:users,id')]
    public string $doctor_id = '';

    #[Validate('required|date')]
    public string $appointment_date = '';

    #[Validate('required')]
    public string $appointment_time = '';

    #[Validate('nullable|string|max:255')]
    public string $reason = '';

    public function mount(): void
    {
        $this->patientId = request()->integer('patient') ?: null;
        $this->appointment_date = now()->format('Y-m-d');
    }

    public function selectPatient(int $id): void
    {
        $this->patientId = $id;
        $this->patientSearch = '';
    }

    public function clearPatient(): void
    {
        $this->patientId = null;
    }

    public function getPatientProperty(): ?Patient
    {
        return $this->patientId
            ? Patient::where('facility_id', Auth::user()->facility_id)->find($this->patientId)
            : null;
    }

    public function getSearchResultsProperty()
    {
        if (mb_strlen($this->patientSearch) < 2) {
            return collect();
        }

        $term = "%{$this->patientSearch}%";

        return Patient::where('facility_id', Auth::user()->facility_id)
            ->where(function ($q) use ($term) {
                $q->where('first_name', 'like', $term)
                    ->orWhere('last_name', 'like', $term)
                    ->orWhere('patient_number', 'like', $term)
                    ->orWhere('phone', 'like', $term);
            })
            ->limit(8)
            ->get();
    }

    public function getDepartmentsProperty()
    {
        return Department::where('facility_id', Auth::user()->facility_id)->orderBy('name')->get();
    }

    public function getDoctorsProperty()
    {
        return User::where('facility_id', Auth::user()->facility_id)->role('doctor')->orderBy('name')->get();
    }

    public function save()
    {
        $this->validate();

        if (! $this->patientId) {
            $this->addError('patientId', 'Please select a patient before booking.');

            return;
        }

        $appointment = Appointment::create([
            'facility_id' => Auth::user()->facility_id,
            'department_id' => $this->department_id,
            'patient_id' => $this->patientId,
            'doctor_id' => $this->doctor_id ?: null,
            'scheduled_at' => "{$this->appointment_date} {$this->appointment_time}",
            'reason' => $this->reason,
            'created_by' => Auth::id(),
        ]);

        $appointment->patient->notify(new AppointmentScheduled($appointment));

        session()->flash('status', "Appointment booked for {$appointment->patient->fullName()} on {$appointment->scheduled_at->format('d M Y H:i')}.");

        return $this->redirect(route('appointments.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.appointments.create');
    }
}
