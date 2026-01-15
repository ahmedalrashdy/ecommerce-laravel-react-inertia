<?php

namespace App\Data\Basic;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class WishlistData extends Data
{
    public function __construct(
        #[LiteralTypeScriptType('WishlistItemData[]')]
        #[DataCollectionOf(WishlistItemData::class)]
        public Collection $items,
        public int $itemsCount,
    ) {
    }
}
