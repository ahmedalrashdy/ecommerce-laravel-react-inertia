<?php

namespace App\Data\Basic;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\Optional;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CategoryData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
        #[MapInputName('image_path')]
        public string $image,
        public int $productsCount,

        #[LiteralTypeScriptType('CategoryData[]')]
        #[DataCollectionOf(self::class)]
        public Lazy|Collection|Optional $children,
    ) {}
}
