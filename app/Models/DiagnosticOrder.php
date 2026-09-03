<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['facility_id', 'patient_id', 'opd_visit_id', 'ordered_by', 'status', 'ordered_at', 'notes'])]
class DiagnosticOrder extends Model
{
    protected function casts(): array
    {
        return [
            'ordered_at' => 'datetime',
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

    public function orderedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ordered_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(DiagnosticOrderItem::class);
    }

    public function refreshStatus(): void
    {
        $this->update([
            'status' => $this->items()->where('status', '!=', 'completed')->doesntExist() ? 'completed' : 'in_progress',
        ]);
    }
}
