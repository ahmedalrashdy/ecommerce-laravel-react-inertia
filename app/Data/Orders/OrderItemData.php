<?php

namespace App\Data\Orders;

use App\Models\CartItem;
use App\Models\ProductVariant;
use Brick\Money\Money;
use Spatie\LaravelData\Data;

class OrderItemData extends Data
{
    public function __construct(
        public int $product_variant_id,
        public int $product_id,
        public string $name,
        public string $sku,
        public string $unit_price,
        public int $quantity,
        public array $options = [],
    ) {}

    public static function fromVariant(ProductVariant $variant, int $quantity): self
    {
        $unitPrice = Money::of($variant->price, 'USD');

        return new self(
            product_variant_id: $variant->id,
            product_id: $variant->product_id,
            name: $variant->product->name,
            sku: $variant->sku,
            unit_price: $unitPrice->getAmount()->toScale(2)->__toString(),
            quantity: $quantity,
            options: $variant->attributeValues->map(function ($attributeValue) {
                return [
                    'attribute_id' => $attributeValue->attribute_id,
                    'value_id' => $attributeValue->id,
                    'name' => $attributeValue->attribute->name,
                    'value' => $attributeValue->value,
                ];
            })->toArray(),
        );
    }

    public static function fromCartItem(CartItem $cartItem): self
    {
        return self::fromVariant($cartItem->productVariant, $cartItem->quantity);
    }
}
