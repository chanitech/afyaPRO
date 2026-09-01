<?php

namespace App\Notifications\Channels;

use App\Models\NotificationLog;
use App\Support\Sms\SmsGateway;
use App\Support\Sms\SmsSendException;
use Illuminate\Notifications\Notification;

class SmsChannel
{
    public function __construct(private readonly SmsGateway $gateway) {}

    public function send(mixed $notifiable, Notification $notification): void
    {
        $to = $notifiable->routeNotificationFor('sms', $notification);

        if (blank($to) || ! method_exists($notification, 'toSms')) {
            return;
        }

        $body = $notification->toSms($notifiable);

        $log = NotificationLog::create([
            'notifiable_type' => $notifiable->getMorphClass(),
            'notifiable_id' => $notifiable->getKey(),
            'channel' => 'sms',
            'type' => $notification::class,
            'to' => $to,
            'body' => $body,
            'status' => 'pending',
        ]);

        try {
            $providerMessageId = $this->gateway->send($to, $body);

            $log->update([
                'status' => 'sent',
                'provider_message_id' => $providerMessageId,
                'sent_at' => now(),
            ]);
        } catch (SmsSendException $e) {
            $log->update([
                'status' => 'failed',
                'error' => $e->getMessage(),
            ]);
        }
    }
}
