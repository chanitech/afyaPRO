<?php

namespace App\Livewire\Reports;

use App\Models\Admission;
use App\Models\Bed;
use App\Models\OpdVisit;
use App\Models\Ward;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public string $startDate;

    public string $endDate;

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->toDateString();
    }

    public function setPeriod(string $preset): void
    {
        $this->endDate = now()->toDateString();

        $this->startDate = match ($preset) {
            'this_month' => now()->startOfMonth()->toDateString(),
            'last_30' => now()->subDays(29)->toDateString(),
            'last_90' => now()->subDays(89)->toDateString(),
            default => $this->startDate,
        };
    }

    private function overlapDays(Carbon $admittedAt, ?Carbon $dischargedAt, Carbon $periodStart, Carbon $periodEnd): float
    {
        $effectiveEnd = $dischargedAt ?? now();
        $overlapStart = $admittedAt->greaterThan($periodStart) ? $admittedAt : $periodStart;
        $overlapEnd = $effectiveEnd->lessThan($periodEnd) ? $effectiveEnd : $periodEnd;

        if ($overlapEnd->lessThanOrEqualTo($overlapStart)) {
            return 0.0;
        }

        return $overlapStart->diffInMinutes($overlapEnd) / 1440;
    }

    /**
     * @return array{beds: int, occupancy: float, discharges: int, alos: ?float, turnover_interval: ?float, bed_turnover_rate: ?float}
     */
    private function statsFor(?int $wardId, int $facilityId, Carbon $start, Carbon $end, int $periodDays): array
    {
        $beds = Bed::where('facility_id', $facilityId)
            ->when($wardId, fn ($q) => $q->where('ward_id', $wardId))
            ->count();

        $overlapping = Admission::where('facility_id', $facilityId)
            ->when($wardId, fn ($q) => $q->where('ward_id', $wardId))
            ->where('admitted_at', '<=', $end)
            ->where(fn ($q) => $q->whereNull('discharged_at')->orWhere('discharged_at', '>=', $start))
            ->get();

        $occupiedBedDays = (float) $overlapping->sum(
            fn (Admission $a) => $this->overlapDays($a->admitted_at, $a->discharged_at, $start, $end)
        );
        $availableBedDays = $beds * $periodDays;

        $discharged = $overlapping->filter(
            fn (Admission $a) => $a->status === 'discharged' && $a->discharged_at?->between($start, $end)
        );
        $dischargeCount = $discharged->count();
        $totalLengthOfStay = (float) $discharged->sum(
            fn (Admission $a) => $a->admitted_at->diffInMinutes($a->discharged_at) / 1440
        );

        return [
            'beds' => $beds,
            'occupancy' => $availableBedDays > 0 ? ($occupiedBedDays / $availableBedDays) * 100 : 0.0,
            'discharges' => $dischargeCount,
            'alos' => $dischargeCount > 0 ? $totalLengthOfStay / $dischargeCount : null,
            'turnover_interval' => $dischargeCount > 0 ? max(0, $availableBedDays - $occupiedBedDays) / $dischargeCount : null,
            'bed_turnover_rate' => $beds > 0 ? $dischargeCount / $beds : null,
        ];
    }

    /**
     * Barber-Johnson chart: plots (Turnover Interval, ALOS) per ward against
     * straight occupancy-percentage reference lines through the origin
     * (Occupancy = ALOS / (ALOS + TI), so ALOS = O/(1-O) x TI).
     *
     * @return array{maxAxis: float, occupancyLines: array, points: array}
     */
    private function buildBarberJohnsonChart(array $wardStats): array
    {
        $values = [1.0];
        foreach ($wardStats as $ward) {
            if ($ward['alos'] !== null) {
                $values[] = $ward['alos'];
                $values[] = $ward['turnover_interval'];
            }
        }
        $maxAxis = max($values) * 1.2;

        // SVG plot area: viewBox is 0 0 480 380, origin sits at bottom-left of the plot.
        $originX = 50;
        $originY = 340;
        $plotW = 415;
        $plotH = 325;
        $toPx = fn (float $x, float $y) => [
            'x' => $originX + ($x / $maxAxis) * $plotW,
            'y' => $originY - ($y / $maxAxis) * $plotH,
        ];

        $occupancyLines = [];
        foreach ([60, 70, 80, 90] as $occupancy) {
            $slope = $occupancy / (100 - $occupancy);
            [$endX, $endY] = $slope <= 1 ? [$maxAxis, $slope * $maxAxis] : [$maxAxis / $slope, $maxAxis];
            $start = $toPx(0, 0);
            $end = $toPx($endX, $endY);
            $occupancyLines[] = [
                'x1' => $start['x'], 'y1' => $start['y'], 'x2' => $end['x'], 'y2' => $end['y'], 'label' => "{$occupancy}%",
            ];
        }

        $points = [];
        foreach ($wardStats as $ward) {
            if ($ward['alos'] === null) {
                continue;
            }
            $px = $toPx($ward['turnover_interval'], $ward['alos']);
            $points[] = ['label' => $ward['name'], 'cx' => $px['x'], 'cy' => $px['y']];
        }

        return [
            'originX' => $originX, 'originY' => $originY, 'plotW' => $plotW, 'plotH' => $plotH,
            'occupancyLines' => $occupancyLines, 'points' => $points,
        ];
    }

    public function render()
    {
        $facilityId = Auth::user()->facility_id;
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end = Carbon::parse($this->endDate)->endOfDay();
        $periodDays = max(1, (int) $start->diffInDays($end) + 1);

        $overall = $this->statsFor(null, $facilityId, $start, $end, $periodDays);

        $wards = Ward::where('facility_id', $facilityId)
            ->orderBy('name')
            ->get()
            ->map(fn (Ward $ward) => array_merge(
                ['id' => $ward->id, 'name' => $ward->name],
                $this->statsFor($ward->id, $facilityId, $start, $end, $periodDays)
            ))
            ->all();

        $currentOccupied = Bed::where('facility_id', $facilityId)->where('status', 'occupied')->count();
        $currentTotal = Bed::where('facility_id', $facilityId)->count();

        $opdVisits = OpdVisit::where('facility_id', $facilityId)->whereBetween('started_at', [$start, $end])->count();

        return view('livewire.reports.dashboard', [
            'overall' => $overall,
            'wards' => $wards,
            'currentOccupied' => $currentOccupied,
            'currentTotal' => $currentTotal,
            'opdVisits' => $opdVisits,
            'opdVisitRate' => $opdVisits / $periodDays,
            'periodDays' => $periodDays,
            'chart' => $this->buildBarberJohnsonChart($wards),
        ]);
    }
}
