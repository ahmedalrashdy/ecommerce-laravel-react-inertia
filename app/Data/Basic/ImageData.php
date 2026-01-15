<?php

namespace App\Data\Basic;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript()]
class ImageData extends Data
{
    public function __construct(
        public string $path,
        public ?string $altText,
        public int $displayOrder,
    ) {}
}
