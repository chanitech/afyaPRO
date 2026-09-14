<?php

namespace App\Livewire\Maternity;

use App\Models\Delivery;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Deliver extends Component
{
    public Delivery $delivery;

    #[Validate('required|in:spontaneous_vaginal,vacuum,forceps,caesarean')]
    public string $delivery_mode = '';

    #[Validate('required|date')]
    public string $delivered_at = '';

    #[Validate('required|in:live_birth,stillbirth')]
    public string $outcome = '';

    #[Validate('nullable|in:male,female')]
    public string $baby_sex = '';

    #[Validate('nullable|integer|min:200|max:6000')]
    public string $baby_weight_grams = '';

    #[Validate('nullable|integer|min:0|max:10')]
    public string $apgar_1min = '';

    #[Validate('nullable|integer|min:0|max:10')]
    public string $apgar_5min = '';

    #[Validate('nullable|string|max:255')]
    public string $perineal_status = '';

    #[Validate('nullable|string|max:2000')]
    public string $complications = '';

    #[Validate('nullable|string|max:2000')]
    public string $notes = '';

    public function mount(Delivery $delivery): void
    {
        $this->delivery = $delivery->load('patient');
        $this->delivered_at = now()->format('Y-m-d\TH:i');
    }

    public function save()
    {
        $this->validate();

        $this->delivery->update([
            'status' => 'delivered',
            'delivery_mode' => $this->delivery_mode,
            'delivered_at' => $this->delivered_at,
            'outcome' => $this->outcome,
            'baby_sex' => $this->baby_sex ?: null,
            'baby_weight_grams' => $this->baby_weight_grams ?: null,
            'apgar_1min' => $this->apgar_1min ?: null,
            'apgar_5min' => $this->apgar_5min ?: null,
            'perineal_status' => $this->perineal_status ?: null,
            'complications' => $this->complications ?: null,
            'notes' => $this->notes ?: null,
        ]);

        session()->flash('status', $this->delivery->patient->fullName().'\'s delivery has been recorded.');

        return $this->redirect(route('maternity.board'), navigate: true);
    }

    public function render()
    {
        return view('livewire.maternity.deliver');
    }
}
