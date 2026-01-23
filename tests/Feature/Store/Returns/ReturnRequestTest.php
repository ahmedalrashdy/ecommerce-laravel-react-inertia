<?php

namespace Tests\Feature\Store\Returns;

use App\Enums\CategoryStatus;
use App\Enums\OrderStatus;
use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ReturnOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ReturnRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_request_return_for_delivered_order(): void
    {
        $user = User::factory()->create();
        $variant = $this->createProductVariant(quantity: 5, price: '120.00');
        $order = Order::factory()->for($user)->create([
            'status' => OrderStatus::DELIVERED,
        ]);
        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_variant_id' => $variant->id,
            'product_id' => $variant->product_id,
            'product_variant_snapshot' => OrderItem::createVariantSnapshot($variant),
            'price' => '120.00',
            'quantity' => 2,
            'discount_amount' => '0.00',
        ]);

        OrderHistory::factory()->create([
            'order_id' => $order->id,
            'status' => OrderStatus::DELIVERED,
            'created_at' => now()->subDays(5),
        ]);

        $response = $this->actingAs($user)->post(route('store.orders.returns', $order), [
            'return_type' => 'partial',
            'reason' => 'المقاس غير مناسب',
            'items' => [
                [
                    'order_item_id' => $orderItem->id,
                    'quantity' => 1,
                    'reason' => 'المقاس غير مناسب',
                ],
            ],
        ]);

        $response->assertRedirect(route('store.orders.show', $order));

        $this->assertDatabaseHas('returns', [
            'order_id' => $order->id,
            'user_id' => $user->id,
        ]);

        $returnOrder = ReturnOrder::where('order_id', $order->id)->first();
        $this->assertNotNull($returnOrder);

        $this->assertDatabaseHas('return_items', [
            'return_id' => $returnOrder->id,
            'order_item_id' => $orderItem->id,
            'quantity' => 1,
        ]);
    }

    public function test_return_request_fails_after_window(): void
    {
        $user = User::factory()->create();
        $variant = $this->createProductVariant(quantity: 5, price: '120.00');
        $order = Order::factory()->for($user)->create([
            'status' => OrderStatus::DELIVERED,
        ]);
        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_variant_id' => $variant->id,
            'product_id' => $variant->product_id,
            'product_variant_snapshot' => OrderItem::createVariantSnapshot($variant),
            'price' => '120.00',
            'quantity' => 1,
            'discount_amount' => '0.00',
        ]);

        OrderHistory::factory()->create([
            'order_id' => $order->id,
            'status' => OrderStatus::DELIVERED,
            'created_at' => now()->subDays(20),
        ]);

        $response = $this->actingAs($user)->post(route('store.orders.returns', $order), [
            'return_type' => 'partial',
            'reason' => 'تجاوزت المهلة',
            'items' => [
                [
                    'order_item_id' => $orderItem->id,
                    'quantity' => 1,
                    'reason' => 'تجاوزت المهلة',
                ],
            ],
        ]);

        $response->assertSessionHasErrors();
    }

    public function test_user_can_request_full_return_with_single_reason(): void
    {
        $user = User::factory()->create();
        $variant = $this->createProductVariant(quantity: 5, price: '120.00');
        $order = Order::factory()->for($user)->create([
            'status' => OrderStatus::DELIVERED,
        ]);
        $firstItem = OrderItem::create([
            'order_id' => $order->id,
            'product_variant_id' => $variant->id,
            'product_id' => $variant->product_id,
            'product_variant_snapshot' => OrderItem::createVariantSnapshot($variant),
            'price' => '120.00',
            'quantity' => 2,
            'discount_amount' => '0.00',
        ]);
        $secondItem = OrderItem::create([
            'order_id' => $order->id,
            'product_variant_id' => $variant->id,
            'product_id' => $variant->product_id,
            'product_variant_snapshot' => OrderItem::createVariantSnapshot($variant),
            'price' => '120.00',
            'quantity' => 1,
            'discount_amount' => '0.00',
        ]);

        OrderHistory::factory()->create([
            'order_id' => $order->id,
            'status' => OrderStatus::DELIVERED,
            'created_at' => now()->subDays(2),
        ]);

        $response = $this->actingAs($user)->post(route('store.orders.returns', $order), [
            'return_type' => 'full',
            'reason' => 'أريد إرجاع كامل الطلب',
        ]);

        $response->assertRedirect(route('store.orders.show', $order));

        $returnOrder = ReturnOrder::where('order_id', $order->id)->first();
        $this->assertNotNull($returnOrder);

        $this->assertDatabaseHas('return_items', [
            'return_id' => $returnOrder->id,
            'order_item_id' => $firstItem->id,
            'quantity' => 2,
        ]);

        $this->assertDatabaseHas('return_items', [
            'return_id' => $returnOrder->id,
            'order_item_id' => $secondItem->id,
            'quantity' => 1,
        ]);
    }

    private function createProductVariant(int $quantity, string $price): ProductVariant
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
        ]);

        return ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'SKU-'.Str::random(8),
            'price' => $price,
            'quantity' => $quantity,
            'is_default' => true,
        ]);
    }
}
