<?php

namespace App\Data\Basic;

use App\Models\OrderItem;
use App\Models\Review;
use Brick\Money\Money;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class OrderDetailsItemData extends Data
{
    /** @param  array<int, array<string, string>>  $attributes */
    public function __construct(
        public int $id,
        public int $productId,
        public string $name,
        public ?string $image,
        public array $attributes,
        public int $quantity,
        public string $price,
        public string $formattedPrice,
        public string $total,
        public string $formattedTotal,
        public ?string $productSlug,
        /** @var array{rating:int,comment:string|null}|null */
        public ?array $review,
    ) {}

    public static function fromModel(OrderItem $item, ?Review $review): self
    {
        $price = Money::of($item->price, 'USD');
        $total = $price->multipliedBy($item->quantity);

        return self::from([
            'id' => $item->id,
            'productId' => $item->product_id,
            'name' => $item->product_name,
            'image' => $item->product_variant_snapshot['variant']['default_image'] ?? null,
            'attributes' => $item->attributes_list,
            'quantity' => $item->quantity,
            'price' => $item->price,
            'formattedPrice' => \App\Data\Casts\MoneyCast::formatMoney($price),
            'total' => $total->getAmount()->toScale(2)->__toString(),
            'formattedTotal' => \App\Data\Casts\MoneyCast::formatMoney($total),
            'productSlug' => $item->product_variant_snapshot['product']['slug'] ?? null,
            'review' => $review
                ? [
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                ]
                : null,
        ]);
    }
}
