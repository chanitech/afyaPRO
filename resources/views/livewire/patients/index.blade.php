<div class="max-w-5xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-medium text-gray-900">Patients</h3>
        <a href="{{ route('patients.create') }}" wire:navigate class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-sm rounded-md hover:bg-gray-700">
            + Register Patient
        </a>
    </div>

    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by name, patient number or phone..."
        class="w-full border-gray-300 rounded-md shadow-sm mb-4" />

    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Patient No.</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Sex / Age</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($patients as $patient)
                    <tr>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $patient->patient_number }}</td>
                        <td class="px-4 py-2 text-sm text-gray-900">{{ $patient->fullName() }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ ucfirst($patient->sex) }}, {{ $patient->age() }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $patient->phone }}</td>
                        <td class="px-4 py-2 text-sm text-right">
                            <a href="{{ route('appointments.create', ['patient' => $patient->id]) }}" wire:navigate class="text-indigo-600 hover:underline">Book appointment</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">No patients found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $patients->links() }}
    </div>
</div>
