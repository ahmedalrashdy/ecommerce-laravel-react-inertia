<?php

namespace App\Data\Basic;

use App\Enums\AttributeType;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;
#[TypeScript()]
class VariantAttributeData extends Data
{
    public function __construct(
        public int $variantId,

        public int $attributeId,
        public string $attributeName,
        public AttributeType $attributeType,

        public int $valueId,
        public string $valueName,
        public string|null $colorCode,
    ) {

    }
}
