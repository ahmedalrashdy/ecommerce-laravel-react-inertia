<?php

namespace App\Data\Basic;

use App\Models\CartItem;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CartItemData extends Data
{
    public function __construct(
        public int $id,
        public int $quantity,
        public bool $isSelected,
        public SimpleProductData $product,
        public SimpleVariantData $productVariant,
    ) {}

    public static function fromModel(CartItem $cartItem): self
    {

        return new self(
            id: $cartItem->id,
            quantity: $cartItem->quantity,
            isSelected: $cartItem->is_selected,
            product: SimpleProductData::fromModel($cartItem->productVariant->product),
            productVariant: SimpleVariantData::fromModel($cartItem->productVariant),
        );
    }
}
