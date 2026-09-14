<?php

namespace App\Livewire\Appointments;

use App\Models\Appointment;
use App\Models\Department;
use App\Models\QueueTicket;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;

class Index extends Component
{
    #[Url]
    public string $date = '';

    #[Url]
    public string $departmentId = '';

    public function mount(): void
    {
        $this->date = $this->date ?: now()->format('Y-m-d');
    }

    public function checkIn(int $appointmentId): void
    {
        $appointment = Appointment::where('facility_id', Auth::user()->facility_id)->findOrFail($appointmentId);

        if ($appointment->status !== 'scheduled') {
            return;
        }

        $today = now()->startOfDay();

        $ticketNumber = QueueTicket::nextTicketNumber($appointment->department, $today);

        QueueTicket::create([
            'facility_id' => $appointment->facility_id,
            'department_id' => $appointment->department_id,
            'patient_id' => $appointment->patient_id,
            'appointment_id' => $appointment->id,
            'queue_date' => $today,
            'ticket_number' => $ticketNumber,
            'status' => 'waiting',
        ]);

        $appointment->update(['status' => 'checked_in']);

        session()->flash('status', "Checked in. Queue ticket #{$ticketNumber} issued.");
    }

    public function render()
    {
        $appointments = Appointment::with(['patient', 'department', 'doctor'])
            ->where('facility_id', Auth::user()->facility_id)
            ->whereDate('scheduled_at', $this->date)
            ->when($this->departmentId, fn ($q) => $q->where('department_id', $this->departmentId))
            ->orderBy('scheduled_at')
            ->paginate(15);

        $departments = Department::where('facility_id', Auth::user()->facility_id)->orderBy('name')->get();

        return view('livewire.appointments.index', compact('appointments', 'departments'));
    }
}
