<?php

namespace App\Models;

use App\Observers\ProductVariantObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy(ProductVariantObserver::class)]
class ProductVariant extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'price' => 'string',
        'compare_at_price' => 'string',
        "is_default"=>"boolean",
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function attributeValuesRaw()
    {
        return $this->hasMany(ProductVariantAttribute::class, 'product_variant_id');
    }

    public function attributeValues()
    {
        return $this->belongsToMany(
            AttributeValue::class,
            'attribute_value_product_variant',
            'product_variant_id',
            'attribute_value_id'
        )->withPivot('attribute_id');
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable')
            ->orderBy('display_order');
    }

    public function defaultImage()
    {

        return $this->morphOne(Image::class, 'imageable')
            ->orderBy('display_order')
            ->limit(1);
    }
}
