<?php

namespace App\Data\Returns;

use App\Enums\ItemCondition;
use App\Enums\ReturnResolution;
use Spatie\LaravelData\Data;

class InspectionResultData extends Data
{
    public function __construct(
        public int $return_item_id,       // ID السطر في جدول return_items
        public ItemCondition $condition,  // SEALED, DAMAGED...
        public ReturnResolution $resolution, // REFUND, REJECT...
        public int $quantity,
        public ?string $note = null,
        public ?float $refund_amount = null,
    ) {}
}
