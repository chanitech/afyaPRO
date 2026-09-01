<div wire:poll.5s class="max-w-5xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-medium text-gray-900">Live Queue</h3>
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

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white shadow-sm rounded-lg p-4">
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-medium text-gray-900">Now Serving / Called</h4>
                <button wire:click="callNext" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-500">
                    Call Next
                </button>
            </div>
            <ul class="divide-y divide-gray-200">
                @forelse ($called as $ticket)
                    <li class="py-3 flex items-center justify-between">
                        <div>
                            <span class="text-2xl font-bold text-indigo-700">#{{ $ticket->ticket_number }}</span>
                            <span class="ml-2 text-sm text-gray-700">{{ $ticket->patient->fullName() }}</span>
                        </div>
                        <span @class([
                            'px-2 py-1 rounded-full text-xs font-medium',
                            'bg-yellow-100 text-yellow-800' => $ticket->status === 'called',
                            'bg-purple-100 text-purple-800' => $ticket->status === 'in_service',
                        ])>
                            {{ str($ticket->status)->headline() }}
                        </span>
                    </li>
                @empty
                    <li class="py-6 text-center text-sm text-gray-500">No one called yet.</li>
                @endforelse
            </ul>
        </div>

        <div class="bg-white shadow-sm rounded-lg p-4">
            <h4 class="font-medium text-gray-900 mb-3">Waiting ({{ $waiting->count() }})</h4>
            <ul class="divide-y divide-gray-200">
                @forelse ($waiting as $ticket)
                    <li class="py-3 flex items-center justify-between">
                        <div>
                            <span class="text-lg font-semibold text-gray-800">#{{ $ticket->ticket_number }}</span>
                            <span class="ml-2 text-sm text-gray-700">{{ $ticket->patient->fullName() }}</span>
                            @if ($ticket->priority === 'urgent')
                                <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Urgent</span>
                            @endif
                        </div>
                        <button wire:click="skip({{ $ticket->id }})" class="text-xs text-gray-500 hover:text-red-600 hover:underline">Skip</button>
                    </li>
                @empty
                    <li class="py-6 text-center text-sm text-gray-500">Queue is empty.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
