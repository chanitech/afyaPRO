<?php

namespace App\Support\Insurance;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LogInsuranceGateway implements InsuranceGateway
{
    public function checkEligibility(string $insurer, string $memberNumber): InsuranceEligibilityResult
    {
        Log::info("{$insurer} eligibility check for member {$memberNumber}");

        return match ($insurer) {
            'nssf' => $this->checkNssf($memberNumber),
            default => $this->checkNhif($memberNumber),
        };
    }

    public function submitClaim(string $insurer, string $claimNumber, string $memberNumber, float $amount): string
    {
        Log::info("{$insurer} claim {$claimNumber} submitted for member {$memberNumber}, amount {$amount}");

        return 'log-'.Str::uuid();
    }

    private function checkNhif(string $memberNumber): InsuranceEligibilityResult
    {
        if (! preg_match('/^\d{6,12}$/', $memberNumber)) {
            return new InsuranceEligibilityResult(
                eligible: false,
                message: 'NHIF card number must be 6-12 digits.',
            );
        }

        return new InsuranceEligibilityResult(
            eligible: true,
            schemeName: 'NHIF Standard Scheme',
        );
    }

    private function checkNssf(string $memberNumber): InsuranceEligibilityResult
    {
        if (! preg_match('/^[A-Za-z0-9-]{6,20}$/', $memberNumber)) {
            return new InsuranceEligibilityResult(
                eligible: false,
                message: 'NSSF member number must be 6-20 letters, digits, or hyphens.',
            );
        }

        return new InsuranceEligibilityResult(
            eligible: true,
            schemeName: 'NSSF Social Health Insurance Benefit (SHIB)',
        );
    }
}
