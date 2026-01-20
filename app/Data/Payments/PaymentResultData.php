<?php

namespace App\Data\Payments;

use App\Enums\TransactionStatus;
use Spatie\LaravelData\Data;

class PaymentResultData extends Data
{
    public function __construct(
        public bool $success,
        public TransactionStatus $status, // PENDING, SUCCESS, FAILED
        public ?string $transactionRef,   // Stripe ID (ch_123...)
        public ?string $errorMessage,     // سبب الفشل
        public array $rawResponse,        // JSON الرد كاملاً للتخزين
    ) {}
}
