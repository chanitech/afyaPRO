<div class="max-w-6xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-medium text-gray-900">Appointments</h3>
        <a href="{{ route('appointments.create') }}" wire:navigate class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-sm rounded-md hover:bg-gray-700">
            + Book Appointment
        </a>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
            {{ session('status') }}
        </div>
    @endif

    <div class="flex flex-wrap gap-4 mb-4">
        <div>
            <x-input-label for="date" value="Date" />
            <x-text-input type="date" wire:model.live="date" id="date" class="mt-1" />
        </div>
        <div>
            <x-input-label for="departmentId" value="Department" />
            <select wire:model.live="departmentId" id="departmentId" class="mt-1 block border-gray-300 rounded-md shadow-sm">
                <option value="">All departments</option>
                @foreach ($departments as $department)
                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Patient</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Doctor</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($appointments as $appointment)
                    <tr>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $appointment->scheduled_at->format('H:i') }}</td>
                        <td class="px-4 py-2 text-sm text-gray-900">{{ $appointment->patient->fullName() }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $appointment->department->name }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $appointment->doctor?->name ?? '—' }}</td>
                        <td class="px-4 py-2 text-sm">
                            <span @class([
                                'px-2 py-1 rounded-full text-xs font-medium',
                                'bg-blue-100 text-blue-800' => $appointment->status === 'scheduled',
                                'bg-yellow-100 text-yellow-800' => $appointment->status === 'checked_in',
                                'bg-purple-100 text-purple-800' => $appointment->status === 'in_consultation',
                                'bg-green-100 text-green-800' => $appointment->status === 'completed',
                                'bg-red-100 text-red-800' => in_array($appointment->status, ['cancelled', 'no_show']),
                            ])>
                                {{ str($appointment->status)->headline() }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-sm text-right">
                            @if ($appointment->status === 'scheduled')
                                <button wire:click="checkIn({{ $appointment->id }})" class="text-indigo-600 hover:underline">Check In</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">No appointments for this date.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $appointments->links() }}
    </div>
</div>
