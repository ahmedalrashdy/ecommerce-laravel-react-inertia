<?php

namespace App\Data\Basic;

use App\Models\Wishlist;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ProductData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
        public ?string $description,
        #[MapInputName('rating_avg')]
        public int $rating,
        #[MapInputName('reviews_count')]
        public int $reviews,
        public int $variantsCount,
        public bool $featured,
        // defaults
        public ProductVariantData $defaultVariant,

    ) {}

    public static function fromWishlistItem(Wishlist $wishlist)
    {
        $variant = $wishlist->productVariant;
        $product = $variant->product;

        return new self(
            id: $product->id,
            name: $product->name,
            slug: $product->slug,
            description: $product->description,
            rating: (int) ($product->rating_avg ?? 0),
            reviews: (int) ($product->reviews_count ?? 0),
            variantsCount: $product->variants_count
            ?? $product->variants()->count(),
            featured: (bool) $product->featured,
            defaultVariant: ProductVariantData::from($variant),
        );
    }
}
