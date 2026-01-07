<?php

namespace Tests\Feature\Observers;

use App\Enums\CategoryStatus;
use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProductVariantObserverTest extends TestCase
{
    use RefreshDatabase;

    public function test_variant_creation_increments_product_variants_count(): void
    {
        $product = $this->createProduct();

        $this->assertSame(0, $product->variants_count);

        ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'SKU-'.Str::random(8),
            'price' => '100.00',
            'quantity' => 5,
            'is_default' => true,
        ]);

        $product->refresh();
        $this->assertSame(1, $product->variants_count);
    }

    public function test_variant_deletion_decrements_product_variants_count(): void
    {
        $product = $this->createProduct();

        $firstVariant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'SKU-'.Str::random(8),
            'price' => '100.00',
            'quantity' => 5,
            'is_default' => true,
        ]);

        $secondVariant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'SKU-'.Str::random(8),
            'price' => '120.00',
            'quantity' => 5,
            'is_default' => false,
        ]);

        $product->refresh();
        $this->assertSame(2, $product->variants_count);

        $secondVariant->delete();

        $product->refresh();
        $this->assertSame(1, $product->variants_count);
        $this->assertDatabaseHas('product_variants', ['id' => $firstVariant->id]);
    }

    private function createProduct(): Product
    {
        $category = Category::create([
            'name' => 'Variants Category',
            'slug' => 'variants-category-'.Str::random(6),
            'description' => null,
            'image_path' => 'categories/variants.png',
            'status' => CategoryStatus::Published,
            'products_count' => 0,
            '_lft' => 1,
            '_rgt' => 2,
        ]);

        return Product::create([
            'category_id' => $category->id,
            'brand_id' => null,
            'name' => 'Variants Product',
            'slug' => 'variants-product-'.Str::random(6),
            'description' => null,
            'specifications' => null,
            'status' => ProductStatus::Published,
            'featured' => false,
            'variants_count' => 0,
        ]);
    }
}
