<?php

namespace App\Data\Basic;

use App\Enums\AttributeType;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript()]
class ProductAttributeData extends Data
{
    public function __construct(

        public int $id,
        public string $name,
        public AttributeType $type,
        #[LiteralTypeScriptType('AttributeValueData|null')]
        public ?AttributeValueData $selectedValue,
        public int $valuesCount,
        #[LiteralTypeScriptType('AttributeValueData[]')]
        #[DataCollectionOf(AttributeValueData::class)]
        public Collection $values,
    ) {}
}
