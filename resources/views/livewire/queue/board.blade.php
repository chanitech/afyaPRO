<div wire:poll.5s>
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h3 class="mb-0">Live Queue</h3>
        <select wire:model.live="departmentId" class="form-select w-auto">
            @foreach ($departments as $department)
                <option value="{{ $department->id }}">{{ $department->name }}</option>
            @endforeach
        </select>
    </div>

    @if (session('status'))
        <x-adminlte-alert theme="success" class="mb-3">
            {{ session('status') }}
        </x-adminlte-alert>
    @endif

    <div class="row g-3">
        <div class="col-md-6">
            <x-adminlte-card title="Now Serving / Called" icon="bi bi-megaphone">
                <x-slot name="tools">
                    <button wire:click="callNext" class="btn btn-primary btn-sm">Call Next</button>
                </x-slot>

                <ul class="list-group list-group-flush">
                    @forelse ($called as $ticket)
                        <li class="list-group-item d-flex align-items-center justify-content-between">
                            <div>
                                <span class="fs-3 fw-bold text-primary">#{{ $ticket->ticket_number }}</span>
                                <span class="ms-2 small">{{ $ticket->patient->fullName() }}</span>
                            </div>
                            <span @class([
                                'badge',
                                'text-bg-warning' => $ticket->status === 'called',
                                'text-bg-info' => $ticket->status === 'in_service',
                            ])>
                                {{ str($ticket->status)->headline() }}
                            </span>
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted py-4">No one called yet.</li>
                    @endforelse
                </ul>
            </x-adminlte-card>
        </div>

        <div class="col-md-6">
            <x-adminlte-card title="Waiting ({{ $waiting->count() }})" icon="bi bi-hourglass-split">
                <ul class="list-group list-group-flush">
                    @forelse ($waiting as $ticket)
                        <li class="list-group-item d-flex align-items-center justify-content-between">
                            <div>
                                <span class="fs-5 fw-semibold">#{{ $ticket->ticket_number }}</span>
                                <span class="ms-2 small">{{ $ticket->patient->fullName() }}</span>
                                @if ($ticket->priority === 'urgent')
                                    <span class="badge text-bg-danger ms-1">Urgent</span>
                                @endif
                            </div>
                            <button wire:click="skip({{ $ticket->id }})" class="btn btn-link btn-sm text-danger">Skip</button>
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted py-4">Queue is empty.</li>
                    @endforelse
                </ul>
            </x-adminlte-card>
        </div>
    </div>
</div>
