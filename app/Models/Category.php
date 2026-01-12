<?php

namespace App\Models;

use App\Enums\CategoryStatus;
use App\Traits\HasFileDeletion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Category extends Model
{
    use HasFactory, HasFileDeletion, HasSlug, NodeTrait;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'image_path',
        'status',
        'products_count',
    ];

    protected function casts(): array
    {
        return [
            'status' => CategoryStatus::class,
        ];
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function scopeWithoutDescendants(Builder $query, Category $category, bool $includeCategory = false)
    {
        return $query
            ->when(! $includeCategory, fn ($q) => $q->where('id', '!=', $category->id))
            ->where(function (Builder $q) use ($category) {
                $q->where('_lft', '<', $category->_lft)
                    ->orWhere('_rgt', '>', $category->_rgt);
            });
    }

    public function deletableFiles(): array
    {
        return [
            'image_path' => 'public',
        ];
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
