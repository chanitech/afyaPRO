<div class="row">
    <div class="col-12 col-lg-9">
        <x-adminlte-card icon="bi bi-clipboard2-pulse"
            title="Diagnostic Order — {{ $order->patient->fullName() }} ({{ $order->patient->patient_number }})">
            <x-slot name="tools">
                <span @class([
                    'badge',
                    'text-bg-warning' => $order->status === 'ordered',
                    'text-bg-info' => $order->status === 'in_progress',
                    'text-bg-success' => $order->status === 'completed',
                ])>
                    {{ str($order->status)->headline() }}
                </span>
            </x-slot>

            <p class="text-muted mb-4">
                Ordered by {{ $order->orderedBy->name }} on {{ $order->ordered_at->format('d M Y H:i') }}
            </p>

            @foreach ($order->items as $item)
                <div class="border rounded p-3 mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="fw-semibold">{{ $item->test->name }}</div>
                        @if ($item->status === 'completed')
                            <span class="badge text-bg-success">Completed</span>
                        @else
                            <span class="badge text-bg-secondary">Pending</span>
                        @endif
                    </div>

                    @if ($item->status === 'completed')
                        <p class="mb-1"><span class="text-muted small">Result:</span> {{ $item->result_value }}</p>
                        @if ($item->result_notes)
                            <p class="mb-1"><span class="text-muted small">Notes:</span> {{ $item->result_notes }}</p>
                        @endif
                        <p class="text-muted small mb-0">
                            Recorded by {{ $item->resultedBy?->name }} on {{ $item->resulted_at?->format('d M Y H:i') }}
                        </p>
                    @else
                        <div class="mb-2">
                            <x-input-label for="result-{{ $item->id }}" value="Result" />
                            <textarea wire:model="resultValue.{{ $item->id }}" id="result-{{ $item->id }}" rows="2" class="form-control"></textarea>
                            <x-input-error :messages="$errors->get('resultValue.'.$item->id)" class="mt-1" />
                        </div>
                        <div class="mb-2">
                            <x-input-label for="notes-{{ $item->id }}" value="Notes (optional)" />
                            <textarea wire:model="resultNotes.{{ $item->id }}" id="notes-{{ $item->id }}" rows="2" class="form-control"></textarea>
                        </div>
                        <div class="text-end">
                            <button type="button" wire:click="recordResult({{ $item->id }})" class="btn btn-primary btn-sm">
                                Save Result
                            </button>
                        </div>
                    @endif
                </div>
            @endforeach
        </x-adminlte-card>
    </div>
</div>
