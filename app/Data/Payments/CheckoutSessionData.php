<?php

namespace App\Data\Payments;

use Spatie\LaravelData\Data;

class CheckoutSessionData extends Data
{
    public function __construct(
        public string $sessionId,
        public string $url,
        public ?string $paymentIntentId,
        public ?int $expiresAt,
        public array $rawResponse,
    ) {}
}
