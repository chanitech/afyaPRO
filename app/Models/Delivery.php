<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'facility_id', 'patient_id', 'opd_visit_id', 'attending_id', 'gravida', 'para',
    'expected_delivery_date', 'labour_onset_at', 'status', 'delivery_mode', 'delivered_at',
    'outcome', 'baby_sex', 'baby_weight_grams', 'apgar_1min', 'apgar_5min',
    'placenta_delivered_at', 'perineal_status', 'complications', 'notes',
])]
class Delivery extends Model
{
    protected function casts(): array
    {
        return [
            'expected_delivery_date' => 'date',
            'labour_onset_at' => 'datetime',
            'delivered_at' => 'datetime',
            'placenta_delivered_at' => 'datetime',
        ];
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function opdVisit(): BelongsTo
    {
        return $this->belongsTo(OpdVisit::class);
    }

    public function attending(): BelongsTo
    {
        return $this->belongsTo(User::class, 'attending_id');
    }

    public function observations(): HasMany
    {
        return $this->hasMany(PartographObservation::class)->orderBy('recorded_at');
    }

    public function hoursSinceOnset(\DateTimeInterface $at): float
    {
        return $this->labour_onset_at->diffInMinutes($at) / 60;
    }
}
