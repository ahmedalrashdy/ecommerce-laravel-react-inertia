<?php

namespace Tests\Feature\Store\Orders;

use App\Enums\CategoryStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ProductStatus;
use App\Enums\ReturnStatus;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ReturnItem;
use App\Models\ReturnOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProductSalesCountTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_count_increments_when_order_is_paid(): void
    {
        $product = $this->createProductWithVariant(salesCount: 0);
        $order = Order::factory()->create([
            'payment_status' => PaymentStatus::PENDING,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_variant_id' => $product->defaultVariant->id,
            'product_id' => $product->id,
            'product_variant_snapshot' => OrderItem::createVariantSnapshot($product->defaultVariant),
            'price' => '120.00',
            'quantity' => 3,
            'discount_amount' => '0.00',
        ]);

        $order->update(['payment_status' => PaymentStatus::PAID]);

        $this->assertSame(3, $product->refresh()->sales_count);
    }

    public function test_sales_count_decrements_when_return_is_completed(): void
    {
        $product = $this->createProductWithVariant(salesCount: 5);
        $order = Order::factory()->create([
            'payment_status' => PaymentStatus::PAID,
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_variant_id' => $product->defaultVariant->id,
            'product_id' => $product->id,
            'product_variant_snapshot' => OrderItem::createVariantSnapshot($product->defaultVariant),
            'price' => '120.00',
            'quantity' => 2,
            'discount_amount' => '0.00',
        ]);

        $returnOrder = ReturnOrder::factory()->create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'status' => ReturnStatus::INSPECTED,
        ]);

        ReturnItem::create([
            'return_id' => $returnOrder->id,
            'order_item_id' => $orderItem->id,
            'quantity' => 2,
            'reason' => 'Damaged',
        ]);

        $returnOrder->update(['status' => ReturnStatus::COMPLETED]);

        $this->assertSame(3, $product->refresh()->sales_count);
    }

    public function test_sales_count_decrements_when_paid_order_is_cancelled(): void
    {
        $product = $this->createProductWithVariant(salesCount: 3);
        $order = Order::factory()->create([
            'payment_status' => PaymentStatus::PAID,
            'status' => OrderStatus::PENDING,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_variant_id' => $product->defaultVariant->id,
            'product_id' => $product->id,
            'product_variant_snapshot' => OrderItem::createVariantSnapshot($product->defaultVariant),
            'price' => '120.00',
            'quantity' => 2,
            'discount_amount' => '0.00',
        ]);

        $order->update([
            'status' => OrderStatus::CANCELLED,
            'cancelled_at' => now(),
            'cancellation_reason' => 'Out of stock',
        ]);

        $this->assertSame(1, $product->refresh()->sales_count);
    }

    public function test_sales_count_increments_when_paid_order_is_created(): void
    {
        $product = $this->createProductWithVariant(salesCount: 0);

        DB::transaction(function () use ($product): void {
            $order = Order::factory()->create([
                'payment_status' => PaymentStatus::PAID,
                'status' => OrderStatus::PROCESSING,
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'product_variant_id' => $product->defaultVariant->id,
                'product_id' => $product->id,
                'product_variant_snapshot' => OrderItem::createVariantSnapshot($product->defaultVariant),
                'price' => '120.00',
                'quantity' => 2,
                'discount_amount' => '0.00',
            ]);
        });

        $this->assertSame(2, $product->refresh()->sales_count);
    }

    private function createProductWithVariant(int $salesCount): Product
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
            'name' => 'Test Product',
            'slug' => 'test-product-'.Str::random(6),
            'description' => null,
            'specifications' => null,
            'status' => ProductStatus::Published,
            'featured' => false,
            'sales_count' => $salesCount,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'SKU-'.Str::random(8),
            'price' => '120.00',
            'quantity' => 5,
            'is_default' => true,
        ]);

        $product->setRelation('defaultVariant', $variant);

        return $product;
    }
}
