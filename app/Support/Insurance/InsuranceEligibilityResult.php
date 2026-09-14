<?php

namespace App\Support\Insurance;

final readonly class InsuranceEligibilityResult
{
    public function __construct(
        public bool $eligible,
        public ?string $schemeName = null,
        public ?string $message = null,
    ) {}
}
