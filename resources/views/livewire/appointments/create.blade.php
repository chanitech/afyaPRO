<div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow-sm rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Book Appointment</h3>

        <div class="mb-6">
            <x-input-label value="Patient" />

            @if ($this->patient)
                <div class="mt-1 flex items-center justify-between border border-gray-300 rounded-md p-3 bg-gray-50">
                    <div>
                        <div class="font-medium text-gray-900">{{ $this->patient->fullName() }}</div>
                        <div class="text-sm text-gray-500">{{ $this->patient->patient_number }} &middot; {{ $this->patient->phone }}</div>
                    </div>
                    <button type="button" wire:click="clearPatient" class="text-sm text-indigo-600 hover:underline">Change</button>
                </div>
            @else
                <div class="relative mt-1">
                    <x-text-input wire:model.live.debounce.300ms="patientSearch" placeholder="Search by name, patient number or phone..." class="block w-full" />

                    @if ($this->searchResults->isNotEmpty())
                        <div class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg max-h-60 overflow-auto">
                            @foreach ($this->searchResults as $result)
                                <button type="button" wire:click="selectPatient({{ $result->id }})"
                                    class="w-full text-left px-4 py-2 hover:bg-gray-100 text-sm">
                                    <span class="font-medium">{{ $result->fullName() }}</span>
                                    <span class="text-gray-500">— {{ $result->patient_number }}</span>
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
                <p class="mt-2 text-sm text-gray-500">
                    Patient not found? <a href="{{ route('patients.create') }}" wire:navigate class="text-indigo-600 hover:underline">Register a new patient</a>.
                </p>
            @endif
            <x-input-error :messages="$errors->get('patientId')" class="mt-2" />
        </div>

        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="department_id" value="Department" />
                    <select wire:model="department_id" id="department_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Select...</option>
                        @foreach ($this->departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="doctor_id" value="Doctor (optional)" />
                    <select wire:model="doctor_id" id="doctor_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Any available doctor</option>
                        @foreach ($this->doctors as $doctor)
                            <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="appointment_date" value="Date" />
                    <x-text-input type="date" wire:model="appointment_date" id="appointment_date" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('appointment_date')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="appointment_time" value="Time" />
                    <x-text-input type="time" wire:model="appointment_time" id="appointment_time" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('appointment_time')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="reason" value="Reason for visit (optional)" />
                <x-text-input wire:model="reason" id="reason" class="mt-1 block w-full" />
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('appointments.index') }}" wire:navigate class="text-sm text-gray-600 hover:underline">Cancel</a>
                <x-primary-button>Book Appointment</x-primary-button>
            </div>
        </form>
    </div>
</div>
