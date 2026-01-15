<?php

namespace App\Data\Basic;

use App\Models\ProductVariant;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\Optional;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ProductVariantData extends Data
{
    public function __construct(
        public int $id,
        public string $sku,

        public string $price,

        public ?string $compareAtPrice,

        public ?int $discountPercent,

        public int $quantity,
        public bool $isDefault,

        #[LiteralTypeScriptType('ImageData[]|undefined')]
        public Lazy|ImageData|Optional $defaultImage,

        #[LiteralTypeScriptType('ImageData[]|undefined')]
        #[DataCollectionOf(ImageData::class)]
        public Lazy|Collection|Optional $images,

    ) {}

    public static function fromModel(ProductVariant $variant): self
    {
        $priceMoney = Money::of($variant->price, 'USD');

        $compareAtMoney = $variant->compare_at_price
            ? Money::of($variant->compare_at_price, 'USD')
            : null;

        $discountPercent = null;
        if ($compareAtMoney && $compareAtMoney->isGreaterThan($priceMoney)) {
            // المعادلة: (الفرق / السعر الأصلي) * 100
            $discountAmount = $compareAtMoney->minus($priceMoney);
            $ratio = $discountAmount->getAmount()->toBigDecimal()
                ->dividedBy(
                    $compareAtMoney->getAmount()->toBigDecimal(),
                    4,
                    RoundingMode::HALF_UP
                );
            $discountPercent = $ratio->multipliedBy(100)
                ->toScale(0, RoundingMode::HALF_UP)
                ->toInt();
        }

        return new self(
            id: $variant->id,
            sku: $variant->sku,
            price: \App\Data\Casts\MoneyCast::formatMoney($priceMoney),
            compareAtPrice: $compareAtMoney
                ? \App\Data\Casts\MoneyCast::formatMoney($compareAtMoney)
                : null,
            discountPercent: $discountPercent,
            quantity: $variant->quantity,
            isDefault: $variant->is_default,
            defaultImage: Lazy::whenLoaded(
                'defaultImage',
                $variant,
                fn () => ImageData::from($variant->defaultImage),
            ),
            images: Lazy::whenLoaded(
                'images',
                $variant,
                fn () => ImageData::collect($variant->images, Collection::class)
            ),
        );
    }
}
