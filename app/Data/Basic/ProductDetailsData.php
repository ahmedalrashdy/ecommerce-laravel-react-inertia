<?php

namespace App\Data\Basic;

use App\Models\Product;
use App\Services\Products\ProductDetailsService;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ProductDetailsData extends Data
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
        public ?BrandData $brand,
        /** @var array<int, array{key: string, value: string}> */
        #[LiteralTypeScriptType('Array<{key: string, value: string}>')]
        public array $specifications,

        #[LiteralTypeScriptType('ProductAttributeData[]')]
        #[DataCollectionOf(ProductAttributeData::class)]
        public Collection $attributes,

        public ProductVariantData $variant,// selected variant (default variant if not selected)
        public bool $featured,
        public bool $isNew = true,
    ) {}

    public static function fromModel(Product $product, array $filters = []): self
    {
        $attributes = app(ProductDetailsService::class)
            ->resolveProductAttributes($product, $filters);

        return self::from([
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'description' => $product->description,
            'rating' => (int) $product->rating_avg,
            'reviews' => (int) $product->reviews_count,
            'variantsCount' => $product->variants_count,
            'brand' => $product->brand ? BrandData::from($product->brand) : null,
            'specifications' => $product->specifications ?? [],
            'featured' => $product->featured,
            'isNew' => true,
            'attributes' => $attributes,
            'variant' => app(ProductDetailsService::class)
                ->currentVariant($product, $attributes),
        ]);

    }
}
