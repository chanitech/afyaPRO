<?php

namespace App\Livewire\Inpatient;

use App\Models\Bed;
use App\Models\Ward;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Wards extends Component
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string|max:20')]
    public string $code = '';

    #[Validate('nullable|string|max:100')]
    public string $ward_type = '';

    /** @var array<int, string> */
    public array $newBedNumber = [];

    public function addWard(): void
    {
        $this->validate();

        Ward::create([
            'facility_id' => Auth::user()->facility_id,
            'name' => $this->name,
            'code' => $this->code,
            'ward_type' => $this->ward_type ?: null,
        ]);

        $this->reset(['name', 'code', 'ward_type']);
        session()->flash('status', 'Ward added.');
    }

    public function addBed(int $wardId): void
    {
        $this->validate([
            "newBedNumber.{$wardId}" => 'required|string|max:20',
        ]);

        $ward = Ward::where('facility_id', Auth::user()->facility_id)->findOrFail($wardId);

        Bed::create([
            'facility_id' => $ward->facility_id,
            'ward_id' => $ward->id,
            'bed_number' => $this->newBedNumber[$wardId],
            'status' => 'available',
        ]);

        unset($this->newBedNumber[$wardId]);
        session()->flash('status', 'Bed added to '.$ward->name.'.');
    }

    public function render()
    {
        $wards = Ward::with('beds')
            ->where('facility_id', Auth::user()->facility_id)
            ->orderBy('name')
            ->get();

        return view('livewire.inpatient.wards', compact('wards'));
    }
}
