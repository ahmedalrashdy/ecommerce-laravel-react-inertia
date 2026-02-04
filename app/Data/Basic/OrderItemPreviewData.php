<?php

namespace App\Data\Basic;

use App\Models\OrderItem;
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

    public static function fromModel(OrderItem $item): self
    {
        return self::from([
            'id' => $item->id,
            'name' => $item->product_name,
            'image' => $item->product_variant_snapshot['variant']['default_image'] ?? null,
        ]);
    }
}
