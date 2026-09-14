<?php

namespace App\Support\Sms;

use Illuminate\Support\Facades\Http;

class BeemSmsGateway implements SmsGateway
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $secretKey,
        private readonly string $sourceAddr,
    ) {}

    public function send(string $to, string $message): ?string
    {
        $response = Http::withBasicAuth($this->apiKey, $this->secretKey)
            ->baseUrl('https://apisms.beem.africa/v1')
            ->post('/send', [
                'source_addr' => $this->sourceAddr,
                'encoding' => 0,
                'message' => $message,
                'recipients' => [
                    ['recipient_id' => 1, 'dest_addr' => $this->normalizePhone($to)],
                ],
            ]);

        if ($response->failed() || ! $response->json('successful')) {
            throw new SmsSendException(
                "Beem SMS send failed: {$response->status()} {$response->body()}"
            );
        }

        return (string) $response->json('request_id');
    }

    private function normalizePhone(string $phone): string
    {
        return ltrim(trim($phone), '+');
    }
}
