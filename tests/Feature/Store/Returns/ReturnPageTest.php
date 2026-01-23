<?php

namespace Tests\Feature\Store\Returns;

use App\Enums\CategoryStatus;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ReturnPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_return_page_when_eligible(): void
    {
        $user = User::factory()->create();
        $variant = $this->createProductVariant(quantity: 5, price: '120.00');
        $order = Order::factory()->for($user)->create([
            'status' => OrderStatus::DELIVERED,
        ]);

        OrderItem::create([
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
            'created_at' => now()->subDays(3),
        ]);

        $response = $this->actingAs($user)->get(route('store.orders.returns.show', $order));

        $response->assertInertia(
            fn ($page) => $page
                ->component('store/orders/return')
                ->where('order.id', $order->id)
                ->has('items', 1)
        );
    }

    public function test_user_cannot_view_return_page_after_window(): void
    {
        $user = User::factory()->create();
        $variant = $this->createProductVariant(quantity: 5, price: '120.00');
        $order = Order::factory()->for($user)->create([
            'status' => OrderStatus::DELIVERED,
        ]);

        OrderItem::create([
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

        $response = $this->actingAs($user)->get(route('store.orders.returns.show', $order));

        $response->assertRedirect(route('store.orders.show', $order));
    }

    public function test_user_cannot_view_return_page_for_return_shipment_order(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create([
            'status' => OrderStatus::DELIVERED,
            'type' => OrderType::RETURN_SHIPMENT,
        ]);

        OrderHistory::factory()->create([
            'order_id' => $order->id,
            'status' => OrderStatus::DELIVERED,
            'created_at' => now()->subDays(3),
        ]);

        $response = $this->actingAs($user)->get(route('store.orders.returns.show', $order));

        $response->assertRedirect(route('store.orders.show', $order));
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
