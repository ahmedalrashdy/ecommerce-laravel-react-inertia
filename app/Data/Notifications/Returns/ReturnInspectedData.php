<?php

namespace App\Data\Notifications\Returns;

use Spatie\LaravelData\Data;

class ReturnInspectedData extends Data
{
    public function __construct(
        public string $return_number,
        public string $order_number,
        public string $title,
        public string $message,
        public string $action_url,
    ) {}
}
