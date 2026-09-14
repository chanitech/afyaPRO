<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['facility_id', 'patient_id', 'opd_visit_id', 'prescribed_by', 'status', 'prescribed_at', 'notes'])]
class Prescription extends Model
{
    protected function casts(): array
    {
        return [
            'prescribed_at' => 'datetime',
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

    public function prescribedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prescribed_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    public function refreshStatus(): void
    {
        $pending = $this->items()->where('status', 'pending')->count();
        $total = $this->items()->count();

        $this->update([
            'status' => match (true) {
                $pending === 0 => 'dispensed',
                $pending < $total => 'partially_dispensed',
                default => 'prescribed',
            },
        ]);
    }
}
