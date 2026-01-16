<?php

namespace Tests\Feature\Store\Products;

use App\Enums\CategoryStatus;
use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ProductsIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_index_defaults_to_new_arrivals_sort(): void
    {
        $category = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics-'.Str::random(6),
            'description' => null,
            'image_path' => 'categories/electronics.png',
            'status' => CategoryStatus::Published,
            'products_count' => 0,
            '_lft' => 1,
            '_rgt' => 2,
        ]);

        $olderProduct = Product::create([
            'category_id' => $category->id,
            'brand_id' => null,
            'name' => 'Older Product',
            'slug' => 'older-product-'.Str::random(6),
            'description' => null,
            'status' => ProductStatus::Published,
            'featured' => false,
            'created_at' => Carbon::now()->subDays(2),
            'updated_at' => Carbon::now()->subDays(2),
        ]);

        $newerProduct = Product::create([
            'category_id' => $category->id,
            'brand_id' => null,
            'name' => 'Newer Product',
            'slug' => 'newer-product-'.Str::random(6),
            'description' => null,
            'status' => ProductStatus::Published,
            'featured' => false,
            'created_at' => Carbon::now()->subDay(),
            'updated_at' => Carbon::now()->subDay(),
        ]);

        ProductVariant::create([
            'product_id' => $olderProduct->id,
            'sku' => 'SKU-OLD-'.Str::random(6),
            'price' => '100.00',
            'quantity' => 5,
            'is_default' => true,
        ]);

        ProductVariant::create([
            'product_id' => $newerProduct->id,
            'sku' => 'SKU-NEW-'.Str::random(6),
            'price' => '120.00',
            'quantity' => 5,
            'is_default' => true,
        ]);

        $response = $this->get(route('store.products.index'));

        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('store/products/index')
                ->where('products.data.0.id', $newerProduct->id)
                ->where('products.data.1.id', $olderProduct->id)
        );
    }

    public function test_products_index_exposes_search_filter(): void
    {
        $category = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics-'.Str::random(6),
            'description' => null,
            'image_path' => 'categories/electronics.png',
            'status' => CategoryStatus::Published,
            'products_count' => 0,
            '_lft' => 1,
            '_rgt' => 2,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'brand_id' => null,
            'name' => 'Alpha Phone',
            'slug' => 'alpha-phone-'.Str::random(6),
            'description' => null,
            'status' => ProductStatus::Published,
            'featured' => false,
        ]);

        ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'SKU-ALPHA-'.Str::random(6),
            'price' => '100.00',
            'quantity' => 5,
            'is_default' => true,
        ]);

        $response = $this->get(route('store.products.index', ['q' => 'Al']));

        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('store/products/index')
                ->where('filters.search', 'Al')
                ->has('products.data', 1)
        );
    }
}
