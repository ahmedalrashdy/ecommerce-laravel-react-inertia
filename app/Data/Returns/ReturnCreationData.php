<?php

namespace App\Data\Returns;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\Hidden;
use Spatie\LaravelData\Data;

class ReturnCreationData extends Data
{
    /**
     * @param  Collection<array<string, int|string>>  $items
     */
    public function __construct(
        #[Hidden]
        public int $orderId,
        public ?string $reason,
        public Collection $items,
    ) {}

    public function getOrderId(): int
    {
        return $this->orderId;
    }
}
