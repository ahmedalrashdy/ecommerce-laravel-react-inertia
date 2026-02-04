<?php

namespace Tests\Feature\Store\Orders;

use App\Enums\CategoryStatus;
use App\Enums\OrderStatus;
use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class OrderDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_order_details(): void
    {
        $user = User::factory()->create();
        $variant = $this->createProductVariant(quantity: 5, price: '120.00');
        $order = Order::factory()->for($user)->create([
            'status' => OrderStatus::SHIPPED,
            'tracking_number' => 'TRK-100',
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

        $response = $this->actingAs($user)->get(route('store.orders.show', $order));

        $response->assertInertia(
            fn ($page) => $page
                ->component('store/orders/show')
                ->where('order.orderNumber', $order->order_number)
                ->where('order.items.0.id', $orderItem->id)
                ->where('order.items.0.name', $variant->product->name)
                ->where('order.trackingNumber', 'TRK-100')
        );
    }

    public function test_user_cannot_view_other_users_order(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create();

        $response = $this->actingAs($user)->get(route('store.orders.show', $order));

        $response->assertStatus(404);
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
