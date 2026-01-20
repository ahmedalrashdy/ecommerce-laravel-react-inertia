<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_variant_id',
        'product_id',
        'product_variant_snapshot',
        'price',
        'quantity',
        'discount_amount',
    ];

    protected $casts = [
        'product_variant_snapshot' => 'array',
        'price' => 'string',
        'discount_amount' => 'string',
    ];

    public static function createVariantSnapshot(ProductVariant $variant): array
    {
        return [
            'product' => [
                'id' => $variant->product->id,
                'name' => $variant->product->name,
                'slug' => $variant->product->slug,
                'category_id' => $variant->product->category_id,
                'brand_id' => $variant->product->brand_id,
                'description' => $variant->product->description,
            ],
            'variant' => [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'price' => $variant->price,
                'compare_at_price' => $variant->compare_at_price,
                'quantity' => $variant->quantity,
                'default_image' => $variant->defaultImage?->path,
                'attributes' => $variant->attributeValues->map(function ($attributeValue) {
                    return [
                        'name' => $attributeValue->attribute->name,
                        'value' => $attributeValue->value,
                        'attribute_id' => $attributeValue->attribute_id,
                        'value_id' => $attributeValue->id,
                    ];
                })->toArray(),
            ],
        ];
    }

    public static function createVaraintSnapshot(ProductVariant $variant): array
    {
        return self::createVariantSnapshot($variant);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function getProductNameAttribute(): string
    {
        return $this->product_variant_snapshot['product']['name'] ?? 'غير متوفر';
    }

    public function getProductSkuAttribute(): string
    {
        return $this->product_variant_snapshot['variant']['sku'] ?? 'غير متوفر';
    }

    public function getAttributesListAttribute(): array
    {
        return $this->product_variant_snapshot['variant']['attributes'] ?? [];
    }

    public function getCompareAtPriceAttribute(): ?string
    {
        return $this->product_variant_snapshot['variant']['compare_at_price'] ?? null;
    }

    protected $appends = ['compare_at_price', 'attributes_list', 'product_sku', 'product_name'];
}
