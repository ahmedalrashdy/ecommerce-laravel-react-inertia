<?php

namespace App\Services\Seed;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Support\Collection;

class StoreDataExporter
{
    /**
     * @return array<string, mixed>
     */
    public function export(): array
    {
        $brands = Brand::query()
            ->orderBy('name')
            ->get()
            ->map(fn (Brand $brand) => [
                'name' => $brand->name,
                'description' => $brand->description,
                'image_path' => $brand->image_path,
                'status' => $brand->status?->value ?? $brand->status,
                'featured' => (bool) $brand->featured,
                'products_count' => $brand->products_count,
            ])
            ->values()
            ->all();

        $categories = Category::query()
            ->defaultOrder()
            ->get()
            ->toTree();

        $reviews = Review::query()
            ->with(['user:id,name,email,gender,is_active', 'product:id,name,slug'])
            ->orderBy('id')
            ->get()
            ->map(fn (Review $review) => [
                'rating' => $review->rating,
                'comment' => $review->comment,
                'is_approved' => (bool) $review->is_approved,
                'created_at' => $review->created_at?->toISOString(),
                'user' => $review->user ? [
                    'name' => $review->user->name,
                    'email' => $review->user->email,
                    'gender' => $review->user->gender,
                    'is_active' => (bool) $review->user->is_active,
                ] : null,
                'product' => $review->product ? [
                    'name' => $review->product->name,
                    'slug' => $review->product->slug,
                ] : null,
            ])
            ->values()
            ->all();

        return [
            'brands' => $brands,
            'categories' => $this->mapCategories($categories),
            'reviews' => $reviews,
        ];
    }

    /**
     * @param  Collection<int, Category>  $categories
     * @return array<int, array<string, mixed>>
     */
    private function mapCategories(Collection $categories): array
    {
        return $categories->map(function (Category $category) {
            $category->loadMissing('children');

            $data = [
                'name' => $category->name,
                'description' => $category->description,
                'image_path' => $category->image_path,
                'status' => $category->status?->value ?? $category->status,
                'products_count' => $category->products_count,
                'specifications' => $category->specifications,
                'children' => $this->mapCategories($category->children),
            ];

            if ($category->children->isEmpty()) {
                $data['products'] = $this->mapProducts($category);
            }

            return $data;
        })->values()->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function mapProducts(Category $category): array
    {
        $products = Product::query()
            ->where('category_id', $category->id)
            ->with([
                'brand:id,name',
                'variants.attributeValues.attribute',
                'variants.images',
            ])
            ->orderBy('id')
            ->get();

        return $products->map(function (Product $product) {
            $attributes = $product->variants
                ->pluck('attributeValues')
                ->flatten()
                ->unique(fn ($value) => $value->attribute_id)
                ->map(fn ($value) => [
                    'name' => $value->attribute->name,
                    'type' => $value->attribute->type?->value ?? $value->attribute->type,
                ])
                ->values()
                ->all();

            $variants = $product->variants->map(function ($variant) {
                $attributeValues = $variant->attributeValues->mapWithKeys(function ($value) {
                    return [
                        $value->attribute->name => [
                            'value' => $value->value,
                            'color_code' => $value->color_code,
                        ],
                    ];
                });

                $images = $variant->images
                    ->sortBy('display_order')
                    ->map(fn ($image) => [
                        'path' => $image->path,
                        'alt' => $image->alt_text,
                        'display_order' => $image->display_order,
                    ])
                    ->values()
                    ->all();

                return [
                    'sku' => $variant->sku,
                    'price' => $variant->price,
                    'compare_at_price' => $variant->compare_at_price,
                    'quantity' => $variant->quantity,
                    'is_default' => (bool) $variant->is_default,
                    'attribute_values' => $attributeValues,
                    'images' => $images,
                ];
            })->values()->all();

            return [
                'name' => $product->name,
                'description' => $product->description,
                'brand' => $product->brand?->name,
                'status' => $product->status?->value ?? $product->status,
                'featured' => (bool) $product->featured,
                'specifications' => $product->specifications,
                'attributes' => $attributes,
                'variants' => $variants,
            ];
        })->values()->all();
    }
}
