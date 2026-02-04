<?php

namespace App\Data\Orders;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Data;

class OrderStatusTransitionData extends Data
{
    public function __construct(
        public OrderStatus $nextStatus,
        public ?string $comment = null,
        public ?Model $actor = null,
        public bool $visibleToUser = true,
        public ?string $trackingNumber = null,
        public bool $notifyCustomer = true,
        public bool $notifyCustomerOnDelivery = true,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function updateAttributes(): array
    {
        $attributes = [];

        if ($this->trackingNumber !== null) {
            $attributes['tracking_number'] = $this->trackingNumber;
        }

        return $attributes;
    }
}
