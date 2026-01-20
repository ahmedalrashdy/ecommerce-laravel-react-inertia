<?php

namespace App\Data\Payments;

use App\Enums\PaymentMethod;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use Spatie\LaravelData\Data;

class TransactionEntryData extends Data
{
    public function __construct(
        public int $order_id,
        public ?int $user_id, // من قام بالدفع؟
        public TransactionType $type, // PAYMENT / REFUND
        public PaymentMethod $payment_method, // ID من Enum (1: Credit, 2: COD)
        public float $amount,
        public string $currency,
        public TransactionStatus $status,
        public ?string $transaction_ref,
        public ?array $gateway_response,
        public ?string $description,
        public ?string $gateway = null,
        public ?string $idempotency_key = null,
        public ?string $event_id = null,
        public ?string $event_type = null,
        public ?string $checkout_session_id = null,
        public ?string $payment_intent_id = null,
    ) {}

}
