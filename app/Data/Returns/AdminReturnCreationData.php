<?php

namespace App\Data\Returns;

use App\Enums\ReturnStatus;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\Hidden;
use Spatie\LaravelData\Data;

class AdminReturnCreationData extends Data
{
    /**
     * @param  Collection<array<string, int|string>>  $items
     */
    public function __construct(
        #[Hidden]
        public int $orderId,
        public ReturnStatus $status,
        public ?string $reason,
        public Collection $items,
    ) {}

    public function getOrderId(): int
    {
        return $this->orderId;
    }
}
