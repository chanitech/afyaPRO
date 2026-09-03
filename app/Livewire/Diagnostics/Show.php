<?php

namespace App\Livewire\Diagnostics;

use App\Models\DiagnosticOrder;
use App\Models\DiagnosticOrderItem;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Show extends Component
{
    public DiagnosticOrder $order;

    /** @var array<int, string> */
    public array $resultValue = [];

    /** @var array<int, string> */
    public array $resultNotes = [];

    public function mount(DiagnosticOrder $order): void
    {
        $this->order = $order->load(['patient', 'orderedBy', 'items.test', 'items.resultedBy']);

        foreach ($this->order->items as $item) {
            $this->resultValue[$item->id] = (string) $item->result_value;
            $this->resultNotes[$item->id] = (string) $item->result_notes;
        }
    }

    public function recordResult(int $itemId): void
    {
        $this->validate([
            'resultValue.'.$itemId => 'required|string|max:2000',
            'resultNotes.'.$itemId => 'nullable|string|max:2000',
        ]);

        $item = DiagnosticOrderItem::where('diagnostic_order_id', $this->order->id)->findOrFail($itemId);

        $item->update([
            'result_value' => $this->resultValue[$itemId],
            'result_notes' => $this->resultNotes[$itemId] ?? null,
            'status' => 'completed',
            'resulted_by' => Auth::id(),
            'resulted_at' => now(),
        ]);

        $this->order->refreshStatus();
        $this->order->refresh()->load(['items.test', 'items.resultedBy']);
    }

    public function render()
    {
        return view('livewire.diagnostics.show');
    }
}
