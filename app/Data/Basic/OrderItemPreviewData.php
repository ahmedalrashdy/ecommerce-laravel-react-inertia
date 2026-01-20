<?php

namespace App\Data\Basic;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class OrderItemPreviewData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $image,
    ) {}
}
