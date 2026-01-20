<?php

namespace App\Data\Orders;

use Spatie\LaravelData\Data;

class CancelOrderResult extends Data
{
    public function __construct(
        public bool $autoRefundAttempted,
        public bool $autoRefundSucceeded,
        public bool $refundRequired,
    ) {}
}
