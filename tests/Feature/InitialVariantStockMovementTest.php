<?php

namespace Tests\Feature;

use App\Enums\CategoryStatus;
use App\Enums\ProductStatus;
use App\Enums\StockMovementType;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class InitialVariantStockMovementTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_variant_with_quantity_creates_supplier_restock_movement(): void
    {
        $product = $this->createProduct();

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'SKU-'.Str::random(8),
            'price' => '120.00',
            'quantity' => 10,
            'is_default' => true,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'product_variant_id' => $variant->id,
            'type' => StockMovementType::SUPPLIER_RESTOCK->value,
            'quantity' => 10,
            'quantity_before' => 0,
            'quantity_after' => 10,
        ]);
    }

    public function test_creating_variant_with_zero_quantity_creates_no_movement(): void
    {
        $product = $this->createProduct();

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'SKU-'.Str::random(8),
            'price' => '120.00',
            'quantity' => 0,
            'is_default' => true,
        ]);

        $this->assertDatabaseMissing('stock_movements', [
            'product_variant_id' => $variant->id,
            'type' => StockMovementType::SUPPLIER_RESTOCK->value,
        ]);
    }

    private function createProduct(): Product
    {
        $category = Category::create([
            'name' => 'Stock Movement Category',
            'slug' => 'stock-movement-category-'.Str::random(6),
            'description' => null,
            'image_path' => 'categories/stock-movement.png',
            'status' => CategoryStatus::Published,
            'products_count' => 0,
            '_lft' => 1,
            '_rgt' => 2,
        ]);

        return Product::create([
            'category_id' => $category->id,
            'brand_id' => null,
            'name' => 'Stock Movement Product',
            'slug' => 'stock-movement-product-'.Str::random(6),
            'description' => null,
            'specifications' => null,
            'status' => ProductStatus::Published,
            'featured' => false,
        ]);
    }
}
