<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'diagnostic_order_id', 'diagnostic_test_id', 'price', 'status',
    'result_value', 'result_notes', 'resulted_by', 'resulted_at',
])]
class DiagnosticOrderItem extends Model
{
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'resulted_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(DiagnosticOrder::class, 'diagnostic_order_id');
    }

    public function test(): BelongsTo
    {
        return $this->belongsTo(DiagnosticTest::class, 'diagnostic_test_id');
    }

    public function resultedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resulted_by');
    }
}
