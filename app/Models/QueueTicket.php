<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

#[Fillable([
    'facility_id', 'department_id', 'patient_id', 'appointment_id', 'queue_date',
    'ticket_number', 'priority', 'status', 'called_at', 'started_at', 'completed_at',
])]
class QueueTicket extends Model
{
    protected function casts(): array
    {
        return [
            'queue_date' => 'date',
            'called_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Atomically issue the next ticket number for a department's queue on a given day.
     */
    public static function nextTicketNumber(Department $department, Carbon $date): int
    {
        return DB::transaction(function () use ($department, $date) {
            $lastNumber = static::where('department_id', $department->id)
                ->whereDate('queue_date', $date)
                ->lockForUpdate()
                ->max('ticket_number');

            return ($lastNumber ?? 0) + 1;
        });
    }

    public function estimatedWaitAhead(): int
    {
        return static::where('department_id', $this->department_id)
            ->whereDate('queue_date', $this->queue_date)
            ->whereIn('status', ['waiting'])
            ->where('ticket_number', '<', $this->ticket_number)
            ->count();
    }
}
