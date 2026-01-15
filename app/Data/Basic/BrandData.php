<?php

namespace App\Data\Basic;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class BrandData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
        #[MapInputName('image_path')]
        public string $image,
        public Optional|int $productsCount,
    
    ) {}
}
