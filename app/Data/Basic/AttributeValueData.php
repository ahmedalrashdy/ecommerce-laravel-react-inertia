<?php

namespace App\Data\Basic;

use Spatie\LaravelData\Data;

class AttributeValueData extends Data
{
    public function __construct(
        public int $id,
        public string $value,
        public ?string $colorCode,
        public bool $enabled = false,
    ) {
    }
}
