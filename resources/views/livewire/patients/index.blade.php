<div>
    <x-adminlte-card title="Patients" icon="bi bi-person-vcard">
        <x-slot name="tools">
            <a href="{{ route('patients.create') }}" wire:navigate class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Register Patient
            </a>
        </x-slot>

        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by name, patient number or phone..."
            class="form-control mb-3" />

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Patient No.</th>
                        <th>Name</th>
                        <th>Sex / Age</th>
                        <th>Phone</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($patients as $patient)
                        <tr>
                            <td>{{ $patient->patient_number }}</td>
                            <td>{{ $patient->fullName() }}</td>
                            <td>{{ ucfirst($patient->sex) }}, {{ $patient->age() }}</td>
                            <td>{{ $patient->phone }}</td>
                            <td class="text-end">
                                <a href="{{ route('appointments.create', ['patient' => $patient->id]) }}" wire:navigate>Book appointment</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No patients found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $patients->links() }}
    </x-adminlte-card>
</div>
