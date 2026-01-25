<?php

namespace App\Models;

use App\Enums\BrandStatus;
use App\Traits\HasFileDeletion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Brand extends Model
{
    use HasFactory;
    use HasFileDeletion;
    use HasSlug;


    public function deletableFiles(): array
    {
        return [
            'image_path' => config('filesystems.default'),
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'image_path',
        'status',
        'featured',
        'products_count',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => BrandStatus::class,
            'featured' => 'boolean',
        ];
    }

    public function imageURL(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? Storage::url($value) : null,
        );
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function scopeFeatured(Builder $query)
    {
        return $query->where('featured', true);
    }

    public function scopePublished(Builder $query)
    {
        return $query->where('status', BrandStatus::Published);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
