<?php

namespace App\Support\Insurance;

/**
 * No public NHIF or NSSF (SHIB) claims API spec is available yet, so only
 * LogInsuranceGateway exists behind this interface. Add an Http-based
 * implementation here once real endpoint/credential details are confirmed
 * with each payer.
 */
interface InsuranceGateway
{
    public function checkEligibility(string $insurer, string $memberNumber): InsuranceEligibilityResult;

    /**
     * Submit a claim and return the payer's reference/tracking id.
     */
    public function submitClaim(string $insurer, string $claimNumber, string $memberNumber, float $amount): string;
}
