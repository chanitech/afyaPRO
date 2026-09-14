<?php

namespace App\Support\Sms;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LogSmsGateway implements SmsGateway
{
    public function send(string $to, string $message): ?string
    {
        Log::info("SMS to {$to}: {$message}");

        return 'log-'.Str::uuid();
    }
}
