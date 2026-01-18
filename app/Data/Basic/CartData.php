<?php

namespace App\Data\Basic;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CartData extends Data
{
    public function __construct(
        public int $id,
        #[LiteralTypeScriptType('CartItemData[]')]
        #[DataCollectionOf(CartItemData::class)]
        public Collection $items,
        public int $itemsCount,
        public int $selectedCount,
        public string $subtotal,
        public string $formattedSubtotal,
        public bool $isAllSelected,
    ) {}
}
