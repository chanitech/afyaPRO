<?php

namespace App\Support\Sms;

interface SmsGateway
{
    /**
     * Send an SMS message and return the provider's message id, or null if unavailable.
     *
     * @throws SmsSendException
     */
    public function send(string $to, string $message): ?string;
}
