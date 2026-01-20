<?php

namespace App\Data\Returns;

use App\Enums\ReturnStatus;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class AdminReturnCreationData extends Data
{
    /**
     * @param  Collection<array<string, int|string>>  $items
     */
    public function __construct(
        public int $orderId,
        public ReturnStatus $status,
        public ?string $reason,
        public Collection $items,
    ) {}
}
