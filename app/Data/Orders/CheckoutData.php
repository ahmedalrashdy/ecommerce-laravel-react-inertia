<?php

namespace App\Data\Orders;

use App\Enums\PaymentMethod;
use App\Models\User;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class CheckoutData extends Data
{
    /**
     * @param  Collection<OrderItemData>  $items
     */
    public function __construct(
        public User $user,
        public AddressData $shippingAddress,
        public PaymentMethod $paymentMethod,
        public Collection $items, // Collection of OrderItemData
        public string $idempotencyKey,
        public ?string $notes = null,
    ) {}
}
