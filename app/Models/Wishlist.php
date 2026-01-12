<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Wishlist extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['user_id', 'product_variant_id', 'created_at'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function product(): HasOneThrough
    {
        return $this->hasOneThrough(
            Product::class,
            ProductVariant::class,
            'id', // Foreign key on product_variants table
            'id', // Foreign key on products table
            'product_variant_id', // Local key on wishlists table
            'product_id' // Local key on product_variants table
        );
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForProductVariant($query, $productVariantId)
    {
        return $query->where('product_variant_id', $productVariantId);
    }

    public function scopeRecent($query)
    {
        return $query->orderByDesc('created_at');
    }
}
