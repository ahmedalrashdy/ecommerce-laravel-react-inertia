<?php

namespace App\Data\Orders;

use Spatie\LaravelData\Data;

class OrderCalculationData extends Data
{
    public function __construct(
        public string $subtotal,
        public string $tax_amount,
        public string $shipping_cost,
        public string $discount_amount,
        public string $grand_total,
        public string $formatted_subtotal,
        public string $formatted_tax_amount,
        public string $formatted_shipping_cost,
        public string $formatted_discount_amount,
        public string $formatted_grand_total,
    ) {}
}
