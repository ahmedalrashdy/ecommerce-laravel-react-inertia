<?php

namespace App\Data\Orders;

use App\Enums\CancelRefundOption;
use App\Models\User;
use Spatie\LaravelData\Data;

class CancelOrderData extends Data
{
    public function __construct(
        public string $reason,
        public ?User $cancelledBy, // null if system
        public bool $isSystemAction = false,
        public CancelRefundOption $refundOption = CancelRefundOption::AUTO,
    ) {}
}
