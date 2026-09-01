<div class="max-w-5xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-medium text-gray-900">OPD Consultation</h3>
        <select wire:model.live="departmentId" class="border-gray-300 rounded-md shadow-sm">
            @foreach ($departments as $department)
                <option value="{{ $department->id }}">{{ $department->name }}</option>
            @endforeach
        </select>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
            {{ session('status') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-1 bg-white shadow-sm rounded-lg p-4">
            <h4 class="font-medium text-gray-900 mb-3">Called Patients</h4>
            <ul class="divide-y divide-gray-200">
                @forelse ($inService as $ticket)
                    <li class="py-3 flex items-center justify-between">
                        <div>
                            <span class="font-semibold text-gray-800">#{{ $ticket->ticket_number }}</span>
                            <span class="ml-2 text-sm text-gray-700">{{ $ticket->patient->fullName() }}</span>
                        </div>
                        @if ($ticket->status === 'called')
                            <button wire:click="start({{ $ticket->id }})" class="text-xs text-indigo-600 hover:underline">Start</button>
                        @else
                            <span class="text-xs text-purple-700">In progress</span>
                        @endif
                    </li>
                @empty
                    <li class="py-6 text-center text-sm text-gray-500">No patients called yet.</li>
                @endforelse
            </ul>
        </div>

        <div class="md:col-span-2 bg-white shadow-sm rounded-lg p-6">
            @if ($activeVisit)
                <h4 class="font-medium text-gray-900 mb-4">
                    Consultation — {{ $activeVisit->patient->fullName() }}
                    <span class="text-sm text-gray-500">({{ $activeVisit->patient->patient_number }})</span>
                </h4>

                <form wire:submit="complete" class="space-y-4">
                    <div>
                        <x-input-label for="chief_complaint" value="Chief complaint" />
                        <textarea wire:model="chief_complaint" id="chief_complaint" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div>
                            <x-input-label for="temperature" value="Temp (°C)" />
                            <x-text-input wire:model="temperature" id="temperature" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <x-input-label for="blood_pressure" value="BP" />
                            <x-text-input wire:model="blood_pressure" id="blood_pressure" placeholder="120/80" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <x-input-label for="pulse" value="Pulse" />
                            <x-text-input wire:model="pulse" id="pulse" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <x-input-label for="weight" value="Weight (kg)" />
                            <x-text-input wire:model="weight" id="weight" class="mt-1 block w-full" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="diagnosis" value="Diagnosis" />
                        <textarea wire:model="diagnosis" id="diagnosis" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>

                    <div>
                        <x-input-label for="notes" value="Notes / treatment plan" />
                        <textarea wire:model="notes" id="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>Complete Visit &amp; Bill</x-primary-button>
                    </div>
                </form>
            @else
                <div class="text-center text-sm text-gray-500 py-12">
                    Select a called patient to start the consultation.
                </div>
            @endif
        </div>
    </div>
</div>
