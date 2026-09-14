<?php

namespace App\Livewire\Queue;

use App\Models\Department;
use App\Models\QueueTicket;
use App\Notifications\QueueTicketCalled;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;

class Board extends Component
{
    #[Url]
    public string $departmentId = '';

    public function mount(): void
    {
        if (! $this->departmentId) {
            $this->departmentId = (string) Department::where('facility_id', Auth::user()->facility_id)
                ->orderBy('name')
                ->value('id');
        }
    }

    public function callNext(): void
    {
        $next = QueueTicket::where('department_id', $this->departmentId)
            ->whereDate('queue_date', now())
            ->where('status', 'waiting')
            ->orderByRaw("priority = 'urgent' desc")
            ->orderBy('ticket_number')
            ->first();

        if (! $next) {
            return;
        }

        $next->update(['status' => 'called', 'called_at' => now()]);

        $next->patient->notify(new QueueTicketCalled($next));

        session()->flash('status', "Ticket #{$next->ticket_number} called.");
    }

    public function skip(int $ticketId): void
    {
        QueueTicket::where('department_id', $this->departmentId)->findOrFail($ticketId)
            ->update(['status' => 'skipped']);
    }

    public function render()
    {
        $waiting = QueueTicket::with('patient')
            ->where('department_id', $this->departmentId)
            ->whereDate('queue_date', now())
            ->where('status', 'waiting')
            ->orderByRaw("priority = 'urgent' desc")
            ->orderBy('ticket_number')
            ->get();

        $called = QueueTicket::with('patient')
            ->where('department_id', $this->departmentId)
            ->whereDate('queue_date', now())
            ->whereIn('status', ['called', 'in_service'])
            ->orderBy('called_at')
            ->get();

        $departments = Department::where('facility_id', Auth::user()->facility_id)->orderBy('name')->get();

        return view('livewire.queue.board', compact('waiting', 'called', 'departments'));
    }
}
