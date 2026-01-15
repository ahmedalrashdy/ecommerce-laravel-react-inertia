<?php

namespace App\Data\Search;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class SearchSuggestionData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
        /** @var 'product'|'category'|'brand' */
        public string $type,
        public ?string $image,
        public ?string $price,
    ) {}
}
