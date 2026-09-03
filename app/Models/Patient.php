<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable([
    'facility_id', 'patient_number', 'first_name', 'last_name', 'date_of_birth', 'sex',
    'phone', 'email', 'national_id', 'address', 'emergency_contact_name',
    'emergency_contact_phone', 'blood_group', 'registered_by',
])]
class Patient extends Model
{
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function queueTickets(): HasMany
    {
        return $this->hasMany(QueueTicket::class);
    }

    public function opdVisits(): HasMany
    {
        return $this->hasMany(OpdVisit::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function diagnosticOrders(): HasMany
    {
        return $this->hasMany(DiagnosticOrder::class);
    }

    public function fullName(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function age(): int
    {
        return $this->date_of_birth->age;
    }

    public function routeNotificationForSms(): ?string
    {
        return $this->phone;
    }

    public static function generatePatientNumber(): string
    {
        return 'PT-'.now()->format('Y').'-'.Str::upper(Str::random(6));
    }
}
