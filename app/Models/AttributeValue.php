<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'attribute_id',
        'value',
        'color_code',
    ];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function productVariants()
    {
        return $this->belongsToMany(
            ProductVariant::class,
            'attribute_value_product_variant',
            'attribute_value_id',
            'product_variant_id'
        );
    }
}
