<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['drug_id', 'type', 'quantity_change', 'reason', 'recorded_by'])]
class DrugStockMovement extends Model
{
    public function drug(): BelongsTo
    {
        return $this->belongsTo(Drug::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
