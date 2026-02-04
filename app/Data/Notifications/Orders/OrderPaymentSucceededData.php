<?php

namespace App\Data\Notifications\Orders;

use Spatie\LaravelData\Attributes\Hidden;
use Spatie\LaravelData\Data;

class OrderPaymentSucceededData extends Data
{
    public function __construct(
        #[Hidden]
        public int $order_id,
        public string $order_number,
        public string $grand_total,
        public string $title,
        public string $message,
        public string $action_url,
    ) {}
}
