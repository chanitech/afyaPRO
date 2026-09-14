<?php

namespace App\Support\Nhif;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LogNhifGateway implements NhifGateway
{
    public function checkEligibility(string $cardNumber): NhifEligibilityResult
    {
        Log::info("NHIF eligibility check for card {$cardNumber}");

        if (! preg_match('/^\d{6,12}$/', $cardNumber)) {
            return new NhifEligibilityResult(
                eligible: false,
                message: 'NHIF card number must be 6-12 digits.',
            );
        }

        return new NhifEligibilityResult(
            eligible: true,
            schemeName: 'NHIF Standard Scheme',
        );
    }

    public function submitClaim(string $claimNumber, string $cardNumber, float $amount): string
    {
        Log::info("NHIF claim {$claimNumber} submitted for card {$cardNumber}, amount {$amount}");

        return 'log-'.Str::uuid();
    }
}
