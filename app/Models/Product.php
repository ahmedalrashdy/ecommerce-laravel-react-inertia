<?php

namespace App\Models;

use App\Enums\ProductStatus;
use App\Observers\ProductObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

#[ObservedBy(ProductObserver::class)]
class Product extends Model
{
    use HasFactory;
    use HasSlug;

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'specifications' => 'array',
            'status' => ProductStatus::class,
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function defaultVariant(): HasOne
    {
        // return is_default or first one
        return $this->hasOne(ProductVariant::class)
            ->orderByDesc('is_default')
            ->orderBy('id')
            ->limit(1);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function category(): BelongsTo
    {
        return $this->BelongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->BelongsTo(Brand::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists(): HasManyThrough
    {
        return $this->hasManyThrough(
            Wishlist::class,
            ProductVariant::class,
            'product_id', // Foreign key on product_variants table
            'product_variant_id', // Foreign key on wishlists table
            'id', // Local key on products table
            'id' // Local key on product_variants table
        );
    }

    public function scopeBestSellers(Builder $query)
    {
        $query->orderByDesc('sales_count');
    }

    public function scopeNewArrivals(Builder $query)
    {
        $query->orderByDesc('created_at');
    }

    public function scopePublished(Builder $query)
    {
        $query->where('products.status', ProductStatus::Published);
    }

    public function scopeDraft(Builder $query)
    {
        $query->where('products.status', ProductStatus::Draft);
    }

    public function scopeWithinCategoryTree(Builder $query, $categorySlugs)
    {
        $slugs = is_array($categorySlugs) ? $categorySlugs : explode(',', $categorySlugs);

        $categories = Category::whereIn('categories.slug', $slugs)->get(['id', '_lft', '_rgt']);

        if ($categories->isEmpty()) {
            return $query;
        }
        $query->joinRelationship('category');

        return $query->where(function ($groupQuery) use ($categories) {
            foreach ($categories as $category) {
                $groupQuery->orWhere(function ($subQuery) use ($category) {
                    $subQuery->where('categories._lft', '>=', $category->_lft)
                        ->where('categories._rgt', '<=', $category->_rgt);
                });
            }
        });
    }
}
