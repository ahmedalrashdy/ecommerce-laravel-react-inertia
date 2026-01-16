<?php

namespace Tests\Feature\Store\Products;

use App\Enums\BrandStatus;
use App\Enums\CategoryStatus;
use App\Enums\ProductStatus;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ProductDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_details_page_includes_specifications(): void
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

        $brand = Brand::create([
            'name' => 'Acme',
            'slug' => 'acme-'.Str::random(6),
            'description' => null,
            'image_path' => 'brands/acme.png',
            'status' => BrandStatus::Published,
            'featured' => false,
            'products_count' => 0,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'name' => 'Test Product',
            'slug' => 'test-product-'.Str::random(6),
            'description' => '<p>Short description.</p>',
            'specifications' => [
                ['key' => 'Weight', 'value' => '10kg'],
                ['key' => 'Material', 'value' => 'Steel'],
            ],
            'status' => ProductStatus::Published,
            'featured' => false,
        ]);

        ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'SKU-'.Str::random(8),
            'price' => '120.00',
            'quantity' => 5,
            'is_default' => true,
        ]);

        $response = $this->get(route('store.products.show', $product));

        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('store/products/product-details')
                ->where('product.id', $product->id)
                ->where('product.description', '<p>Short description.</p>')
                ->where('product.specifications.0.key', 'Weight')
                ->where('product.specifications.0.value', '10kg')
                ->where('product.specifications.1.key', 'Material')
                ->where('product.specifications.1.value', 'Steel')
                ->missing('reviewsSummary')
                ->missing('reviews')
        );
    }

    public function test_reviews_are_loaded_on_partial_reload(): void
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

        $brand = Brand::create([
            'name' => 'Acme',
            'slug' => 'acme-'.Str::random(6),
            'description' => null,
            'image_path' => 'brands/acme.png',
            'status' => BrandStatus::Published,
            'featured' => false,
            'products_count' => 0,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'name' => 'Test Product',
            'slug' => 'test-product-'.Str::random(6),
            'description' => '<p>Short description.</p>',
            'specifications' => [],
            'status' => ProductStatus::Published,
            'featured' => false,
        ]);

        ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'SKU-'.Str::random(8),
            'price' => '120.00',
            'quantity' => 5,
            'is_default' => true,
        ]);

        $user = User::factory()->create();

        Review::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rating' => 5,
            'comment' => 'تقييم رائع',
            'is_approved' => true,
        ]);

        $response = $this->get(route('store.products.show', $product).'?reviews=1', [
            'X-Inertia-Partial-Component' => 'store/products/product-details',
            'X-Inertia-Partial-Data' => 'reviewsSummary,reviews',
        ]);

        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('store/products/product-details')
                ->where('reviewsSummary.totalReviews', 1)
                ->has('reviews')
        );
    }
}
