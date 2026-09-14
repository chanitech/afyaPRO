<?php

namespace App\Livewire\Maternity;

use App\Models\Delivery;
use App\Models\OpdVisit;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Admit extends Component
{
    public OpdVisit $visit;

    #[Validate('nullable|integer|min:0|max:20')]
    public string $gravida = '';

    #[Validate('nullable|integer|min:0|max:20')]
    public string $para = '';

    #[Validate('nullable|date')]
    public string $expected_delivery_date = '';

    #[Validate('required|date')]
    public string $labour_onset_at = '';

    public function mount(OpdVisit $visit): void
    {
        $this->visit = $visit->loadMissing('patient');
        $this->labour_onset_at = now()->format('Y-m-d\TH:i');

        if ($this->visit->delivery) {
            $this->redirect(route('maternity.partograph', ['delivery' => $this->visit->delivery->id]), navigate: true);
        }
    }

    public function save()
    {
        $this->validate();

        $delivery = Delivery::create([
            'facility_id' => $this->visit->facility_id,
            'patient_id' => $this->visit->patient_id,
            'opd_visit_id' => $this->visit->id,
            'attending_id' => Auth::id(),
            'gravida' => $this->gravida !== '' ? $this->gravida : null,
            'para' => $this->para !== '' ? $this->para : null,
            'expected_delivery_date' => $this->expected_delivery_date ?: null,
            'labour_onset_at' => $this->labour_onset_at,
            'status' => 'in_labour',
        ]);

        return $this->redirect(route('maternity.partograph', ['delivery' => $delivery->id]), navigate: true);
    }

    public function render()
    {
        return view('livewire.maternity.admit');
    }
}
