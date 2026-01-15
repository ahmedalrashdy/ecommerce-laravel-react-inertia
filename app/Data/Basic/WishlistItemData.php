<?php

namespace App\Data\Basic;

use App\Models\Wishlist;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class WishlistItemData extends Data
{
    public function __construct(
        public int $id,
        public SimpleProductData $product,
        public SimpleVariantData $productVariant,
    ) {}

    public static function fromModel(Wishlist $wishlist): self
    {
        return new self(
            id: $wishlist->id,
            product: SimpleProductData::fromModel($wishlist->productVariant->product),
            productVariant: SimpleVariantData::fromModel($wishlist->productVariant),
        );
    }
}
