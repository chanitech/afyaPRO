<?php

namespace App\Livewire\Maternity;

use App\Models\Delivery;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Partograph extends Component
{
    public Delivery $delivery;

    #[Validate('required|date')]
    public string $recorded_at = '';

    #[Validate('nullable|integer|min:60|max:220')]
    public string $fetal_heart_rate = '';

    #[Validate('nullable|integer|min:0|max:10')]
    public string $cervical_dilation_cm = '';

    #[Validate('nullable|integer|min:0|max:5')]
    public string $descent_fifths = '';

    #[Validate('nullable|integer|min:0|max:10')]
    public string $contractions_per_10min = '';

    #[Validate('nullable|integer|min:0|max:600')]
    public string $contraction_duration_seconds = '';

    #[Validate('nullable|in:intact,clear,meconium,blood_stained,absent')]
    public string $liquor = '';

    #[Validate('nullable|in:0,+,++,+++')]
    public string $moulding = '';

    #[Validate('nullable|integer|min:40|max:200')]
    public string $maternal_pulse = '';

    #[Validate('nullable|integer|min:50|max:250')]
    public string $maternal_bp_systolic = '';

    #[Validate('nullable|integer|min:30|max:150')]
    public string $maternal_bp_diastolic = '';

    #[Validate('nullable|numeric|min:30|max:43')]
    public string $maternal_temperature = '';

    public function mount(Delivery $delivery): void
    {
        $this->delivery = $delivery->load(['patient', 'attending', 'observations.recordedBy']);
        $this->recorded_at = now()->format('Y-m-d\TH:i');
    }

    public function addObservation(): void
    {
        $this->validate();

        $this->delivery->observations()->create([
            'recorded_at' => $this->recorded_at,
            'fetal_heart_rate' => $this->fetal_heart_rate ?: null,
            'cervical_dilation_cm' => $this->cervical_dilation_cm ?: null,
            'descent_fifths' => $this->descent_fifths ?: null,
            'contractions_per_10min' => $this->contractions_per_10min ?: null,
            'contraction_duration_seconds' => $this->contraction_duration_seconds ?: null,
            'liquor' => $this->liquor ?: null,
            'moulding' => $this->moulding ?: null,
            'maternal_pulse' => $this->maternal_pulse ?: null,
            'maternal_bp_systolic' => $this->maternal_bp_systolic ?: null,
            'maternal_bp_diastolic' => $this->maternal_bp_diastolic ?: null,
            'maternal_temperature' => $this->maternal_temperature ?: null,
            'recorded_by' => Auth::id(),
        ]);

        $this->delivery->refresh()->load('observations.recordedBy');

        $this->reset([
            'fetal_heart_rate', 'cervical_dilation_cm', 'descent_fifths', 'contractions_per_10min',
            'contraction_duration_seconds', 'liquor', 'moulding', 'maternal_pulse',
            'maternal_bp_systolic', 'maternal_bp_diastolic', 'maternal_temperature',
        ]);
        $this->recorded_at = now()->format('Y-m-d\TH:i');
    }

    /**
     * WHO-style cervicograph: cervical dilation (y, 0-10cm) vs hours since
     * labour onset (x). The alert line starts at 4cm from the first
     * observation reaching 4cm dilation and rises at 1cm/hour; the action
     * line is the same slope, offset 4 hours later.
     *
     * @return array{maxHours: float, originX: float, originY: float, plotW: float, plotH: float, dilationPoints: array, alertLine: ?array, actionLine: ?array}
     */
    private function buildCervicograph(): array
    {
        $dilationObservations = $this->delivery->observations
            ->filter(fn ($o) => $o->cervical_dilation_cm !== null)
            ->values();

        $lastHour = $dilationObservations->isNotEmpty()
            ? $this->delivery->hoursSinceOnset($dilationObservations->last()->recorded_at)
            : 0.0;

        $activePhaseStart = $dilationObservations->first(fn ($o) => $o->cervical_dilation_cm >= 4);
        $referenceHour = $activePhaseStart ? $this->delivery->hoursSinceOnset($activePhaseStart->recorded_at) : null;

        $maxHours = max(14.0, $lastHour + 1, $referenceHour !== null ? $referenceHour + 11 : 0);

        $originX = 50;
        $originY = 320;
        $plotW = 400;
        $plotH = 280;
        $toPx = fn (float $hours, float $cm) => [
            'x' => $originX + ($hours / $maxHours) * $plotW,
            'y' => $originY - ($cm / 10) * $plotH,
        ];

        $dilationPoints = $dilationObservations
            ->map(fn ($o) => $toPx($this->delivery->hoursSinceOnset($o->recorded_at), (float) $o->cervical_dilation_cm))
            ->all();

        $alertLine = null;
        $actionLine = null;
        if ($referenceHour !== null) {
            $alertLine = [$toPx($referenceHour, 4), $toPx(min($maxHours, $referenceHour + 6), 10)];
            $actionLine = [$toPx($referenceHour + 4, 4), $toPx(min($maxHours, $referenceHour + 10), 10)];
        }

        return [
            'originX' => $originX, 'originY' => $originY, 'plotW' => $plotW, 'plotH' => $plotH,
            'dilationPoints' => $dilationPoints, 'alertLine' => $alertLine, 'actionLine' => $actionLine,
        ];
    }

    public function render()
    {
        return view('livewire.maternity.partograph', [
            'chart' => $this->buildCervicograph(),
        ]);
    }
}
