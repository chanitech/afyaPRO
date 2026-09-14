<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'delivery_id', 'recorded_at', 'fetal_heart_rate', 'cervical_dilation_cm', 'descent_fifths',
    'contractions_per_10min', 'contraction_duration_seconds', 'liquor', 'moulding',
    'maternal_pulse', 'maternal_bp_systolic', 'maternal_bp_diastolic', 'maternal_temperature',
    'urine_protein', 'urine_acetone', 'oxytocin_units', 'drugs_given', 'recorded_by',
])]
class PartographObservation extends Model
{
    protected function casts(): array
    {
        return [
            'recorded_at' => 'datetime',
            'maternal_temperature' => 'decimal:1',
            'oxytocin_units' => 'decimal:1',
        ];
    }

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
