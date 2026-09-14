<?php

namespace App\Support\Nhif;

final readonly class NhifEligibilityResult
{
    public function __construct(
        public bool $eligible,
        public ?string $schemeName = null,
        public ?string $message = null,
    ) {}
}
