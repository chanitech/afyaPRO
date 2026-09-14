<?php

namespace App\Support\Nhif;

/**
 * No public NHIF claims API spec is available yet, so only LogNhifGateway
 * exists behind this interface. Add an Http-based implementation here once
 * real endpoint/credential details are confirmed with NHIF.
 */
interface NhifGateway
{
    public function checkEligibility(string $cardNumber): NhifEligibilityResult;

    /**
     * Submit a claim and return the payer's reference/tracking id.
     */
    public function submitClaim(string $claimNumber, string $cardNumber, float $amount): string;
}
