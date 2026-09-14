<div>
    <x-adminlte-card title="Appointments" icon="bi bi-calendar2-check">
        <x-slot name="tools">
            <a href="{{ route('appointments.create') }}" wire:navigate class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Book Appointment
            </a>
        </x-slot>

        @if (session('status'))
            <x-adminlte-alert theme="success" class="mb-3">
                {{ session('status') }}
            </x-adminlte-alert>
        @endif

        <div class="row g-3 mb-3">
            <div class="col-auto">
                <x-input-label for="date" value="Date" />
                <x-text-input type="date" wire:model.live="date" id="date" />
            </div>
            <div class="col-auto">
                <x-input-label for="departmentId" value="Department" />
                <select wire:model.live="departmentId" id="departmentId" class="form-select">
                    <option value="">All departments</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Patient</th>
                        <th>Department</th>
                        <th>Doctor</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($appointments as $appointment)
                        <tr>
                            <td>{{ $appointment->scheduled_at->format('H:i') }}</td>
                            <td>{{ $appointment->patient->fullName() }}</td>
                            <td>{{ $appointment->department->name }}</td>
                            <td>{{ $appointment->doctor?->name ?? '—' }}</td>
                            <td>
                                <span @class([
                                    'badge',
                                    'text-bg-primary' => $appointment->status === 'scheduled',
                                    'text-bg-warning' => $appointment->status === 'checked_in',
                                    'text-bg-info' => $appointment->status === 'in_consultation',
                                    'text-bg-success' => $appointment->status === 'completed',
                                    'text-bg-danger' => in_array($appointment->status, ['cancelled', 'no_show']),
                                ])>
                                    {{ str($appointment->status)->headline() }}
                                </span>
                            </td>
                            <td class="text-end">
                                @if ($appointment->status === 'scheduled')
                                    <button wire:click="checkIn({{ $appointment->id }})" class="btn btn-outline-primary btn-sm">Check In</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No appointments for this date.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $appointments->links() }}
    </x-adminlte-card>
</div>
