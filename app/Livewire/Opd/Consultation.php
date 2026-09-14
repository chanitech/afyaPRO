<?php

namespace App\Livewire\Opd;

use App\Models\Department;
use App\Models\OpdVisit;
use App\Models\QueueTicket;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Consultation extends Component
{
    #[Url]
    public string $departmentId = '';

    public ?int $activeVisitId = null;

    #[Validate('nullable|string|max:1000')]
    public string $chief_complaint = '';

    #[Validate('nullable|numeric')]
    public string $temperature = '';

    #[Validate('nullable|string|max:20')]
    public string $blood_pressure = '';

    #[Validate('nullable|numeric')]
    public string $pulse = '';

    #[Validate('nullable|numeric')]
    public string $weight = '';

    #[Validate('nullable|string|max:2000')]
    public string $diagnosis = '';

    #[Validate('nullable|string|max:2000')]
    public string $notes = '';

    public function mount(): void
    {
        if (! $this->departmentId) {
            $this->departmentId = (string) Department::where('facility_id', Auth::user()->facility_id)
                ->orderBy('name')
                ->value('id');
        }
    }

    public function start(int $ticketId): void
    {
        $ticket = QueueTicket::where('department_id', $this->departmentId)->findOrFail($ticketId);

        // Reuse an already-open visit for this patient (e.g. the doctor navigated
        // away to order tests and came back) instead of creating a duplicate.
        $visit = OpdVisit::where('patient_id', $ticket->patient_id)
            ->where('department_id', $ticket->department_id)
            ->where('status', 'in_progress')
            ->latest('started_at')
            ->first();

        if (! $visit) {
            $visit = OpdVisit::create([
                'facility_id' => $ticket->facility_id,
                'department_id' => $ticket->department_id,
                'patient_id' => $ticket->patient_id,
                'doctor_id' => Auth::id(),
                'appointment_id' => $ticket->appointment_id,
                'status' => 'in_progress',
                'started_at' => now(),
            ]);
        }

        $ticket->update(['status' => 'in_service', 'started_at' => $ticket->started_at ?? now()]);

        $this->activeVisitId = $visit->id;
        $this->chief_complaint = (string) $visit->chief_complaint;
        $this->temperature = (string) ($visit->vitals['temperature'] ?? '');
        $this->blood_pressure = (string) ($visit->vitals['blood_pressure'] ?? '');
        $this->pulse = (string) ($visit->vitals['pulse'] ?? '');
        $this->weight = (string) ($visit->vitals['weight'] ?? '');
        $this->diagnosis = (string) $visit->diagnosis;
        $this->notes = (string) $visit->notes;
    }

    public function complete()
    {
        $this->validate();

        $visit = OpdVisit::findOrFail($this->activeVisitId);

        $visit->update([
            'chief_complaint' => $this->chief_complaint,
            'vitals' => array_filter([
                'temperature' => $this->temperature,
                'blood_pressure' => $this->blood_pressure,
                'pulse' => $this->pulse,
                'weight' => $this->weight,
            ]),
            'diagnosis' => $this->diagnosis,
            'notes' => $this->notes,
            'status' => 'completed',
            'ended_at' => now(),
        ]);

        QueueTicket::where('patient_id', $visit->patient_id)
            ->where('department_id', $visit->department_id)
            ->whereDate('queue_date', now())
            ->where('status', 'in_service')
            ->update(['status' => 'done', 'completed_at' => now()]);

        if ($visit->appointment) {
            $visit->appointment->update(['status' => 'completed']);
        }

        $this->activeVisitId = null;

        session()->flash('status', "Visit for {$visit->patient->fullName()} completed and sent to billing.");

        return $this->redirect(route('opd.consultation', ['departmentId' => $this->departmentId]), navigate: true);
    }

    public function render()
    {
        $inService = QueueTicket::with('patient')
            ->where('department_id', $this->departmentId)
            ->whereDate('queue_date', now())
            ->whereIn('status', ['called', 'in_service'])
            ->orderBy('called_at')
            ->get();

        $departments = Department::where('facility_id', Auth::user()->facility_id)->orderBy('name')->get();

        $activeVisit = $this->activeVisitId
            ? OpdVisit::with(['patient', 'diagnosticOrders.items.test', 'prescriptions.items.drug', 'admission.ward', 'admission.bed', 'delivery'])->find($this->activeVisitId)
            : null;

        return view('livewire.opd.consultation', compact('inService', 'departments', 'activeVisit'));
    }
}
