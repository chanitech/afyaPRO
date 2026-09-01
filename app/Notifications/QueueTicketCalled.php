<?php

namespace App\Notifications;

use App\Models\QueueTicket;
use App\Notifications\Channels\SmsChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class QueueTicketCalled extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly QueueTicket $ticket) {}

    public function via(mixed $notifiable): array
    {
        return [SmsChannel::class];
    }

    public function toSms(mixed $notifiable): string
    {
        return sprintf(
            'AfyaPRO: Ticket #%d, please proceed to %s now.',
            $this->ticket->ticket_number,
            $this->ticket->department->name,
        );
    }
}
