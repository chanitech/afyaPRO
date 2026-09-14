<?php

namespace App\Notifications;

use App\Models\Appointment;
use App\Notifications\Channels\SmsChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AppointmentScheduled extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly Appointment $appointment) {}

    public function via(mixed $notifiable): array
    {
        return [SmsChannel::class];
    }

    public function toSms(mixed $notifiable): string
    {
        $appointment = $this->appointment;

        return sprintf(
            'AfyaPRO: Your appointment at %s is booked for %s. Please arrive 15 min early.',
            $appointment->facility->name,
            $appointment->scheduled_at->format('D, d M Y H:i'),
        );
    }
}
