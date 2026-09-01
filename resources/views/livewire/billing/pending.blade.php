<div class="max-w-5xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <h3 class="text-lg font-medium text-gray-900 mb-4">Visits Awaiting Billing</h3>

    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Completed</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Patient</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Doctor</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Diagnosis</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($visits as $visit)
                    <tr>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $visit->ended_at?->format('d M Y H:i') }}</td>
                        <td class="px-4 py-2 text-sm text-gray-900">{{ $visit->patient->fullName() }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $visit->doctor->name }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ str($visit->diagnosis)->limit(40) }}</td>
                        <td class="px-4 py-2 text-sm text-right">
                            <a href="{{ route('billing.create', ['visit' => $visit->id]) }}" wire:navigate class="text-indigo-600 hover:underline">Create Invoice</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">No visits awaiting billing.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $visits->links() }}
    </div>
</div>
