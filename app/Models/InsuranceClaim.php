<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable([
    'facility_id', 'patient_id', 'invoice_id', 'claim_number', 'insurer',
    'member_number', 'scheme_name', 'amount_claimed', 'amount_approved',
    'status', 'rejection_reason', 'eligibility_checked_at', 'submitted_at',
    'responded_at', 'submitted_by',
])]
class InsuranceClaim extends Model
{
    public const INSURERS = [
        'nhif' => 'NHIF',
        'nssf' => 'NSSF (SHIB)',
    ];

    protected function casts(): array
    {
        return [
            'amount_claimed' => 'decimal:2',
            'amount_approved' => 'decimal:2',
            'eligibility_checked_at' => 'datetime',
            'submitted_at' => 'datetime',
            'responded_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (InsuranceClaim $claim) {
            $prefix = Str::upper($claim->insurer ?: 'INS');
            $claim->claim_number ??= "{$prefix}-".now()->format('Ymd').'-'.Str::upper(Str::random(6));
        });
    }

    public function insurerLabel(): string
    {
        return self::INSURERS[$this->insurer] ?? Str::upper($this->insurer);
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }
}
