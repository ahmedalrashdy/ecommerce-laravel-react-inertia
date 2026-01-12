<?php

namespace App\Observers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        if ($product->brand_id) {
            $product->brand()->increment('products_count');
        }

        if ($product->category_id) {
            $category = Category::find($product->category_id);
            if ($category) {
                $this->updateCategoryCounts($category, 1);
            }
        }

    }

    public function deleted(Product $product): void
    {
        if ($product->brand_id) {
            $product->brand()->decrement('products_count');
        }

        if ($product->category_id) {
            $category = Category::find($product->category_id);
            if ($category) {
                $this->updateCategoryCounts($category, -1);
            }
        }
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        if ($product->wasChanged('brand_id')) {
            $originalBrandId = $product->getOriginal('brand_id');
            if ($originalBrandId) {
                Brand::where('id', $originalBrandId)
                    ->decrement('products_count');
            }

            if ($product->brand_id) {
                $product->brand()->increment('products_count');
            }
        }

        if ($product->wasChanged('category_id')) {
            $originalCategoryId = $product->getOriginal('category_id');
            if ($originalCategoryId) {
                $originalCategory = Category::find($originalCategoryId);
                if ($originalCategory) {
                    $this->updateCategoryCounts($originalCategory, -1);
                }
            }

            if ($product->category_id) {
                $category = Category::find($product->category_id);
                if ($category) {
                    $this->updateCategoryCounts($category, 1);
                }
            }
        }
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {
        //
    }

    private function updateCategoryCounts(Category $category, int $amount): void
    {
        $categoryIds = Category::ancestorsAndSelf($category->id)->pluck('id');

        if ($categoryIds->isEmpty()) {
            return;
        }

        if ($amount > 0) {
            Category::whereIn('id', $categoryIds)->increment('products_count', $amount);

            return;
        }

        Category::whereIn('id', $categoryIds)->decrement('products_count', abs($amount));
    }
}
