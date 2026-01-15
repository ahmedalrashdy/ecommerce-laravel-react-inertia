<?php

namespace App\Data\Basic;

use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class SimpleVariantData extends Data
{
    public function __construct(
        public int $id,
        public string $price,
        public int $quantity,
        public ?ImageData $defaultImage,
        #[LiteralTypeScriptType('VariantAttributeData[]')]
        #[DataCollectionOf(VariantAttributeData::class)]
        public Collection $attributes,
    ) {}

    public static function fromModel(ProductVariant $variant): self
    {
        return new self(
            id: $variant->id,
            price: $variant->price,
            quantity: $variant->quantity,
            defaultImage: $variant->defaultImage ? ImageData::from($variant->defaultImage) : null,
            attributes: $variant->attributeValues
                ->map(fn ($attributeValue) => VariantAttributeData::from([
                    'variantId' => $variant->id,
                    'attributeId' => $attributeValue->attribute_id,
                    'attributeName' => $attributeValue->attribute->name,
                    'attributeType' => $attributeValue->attribute->type,
                    'valueId' => $attributeValue->id,
                    'valueName' => $attributeValue->value,
                    'colorCode' => $attributeValue->color_code,
                ])),
        );
    }
}
