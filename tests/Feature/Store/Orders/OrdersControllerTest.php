<?php

namespace Tests\Feature\Store\Orders;

use App\Enums\BrandStatus;
use App\Enums\CategoryStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ProductStatus;
use App\Models\Brand;
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

class OrdersControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_order_details(): void
    {
        $user = User::factory()->create();
        $variant = $this->createProductVariant(quantity: 2, price: '120.00');
        $order = Order::factory()->for($user)->create([
            'status' => OrderStatus::DELIVERED,
            'payment_status' => PaymentStatus::PAID,
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

        $response = $this->actingAs($user)->get(route('store.account.orders.show', $order));

        $response->assertInertia(
            fn ($page) => $page
                ->component('store/account/orders/show')
                ->where('order.id', $order->id)
                ->where('order.orderNumber', $order->order_number)
                ->where('order.canReturn', true)
                ->has('order.items', 1)
                ->has('summary.formattedGrandTotal')
        );
    }

    public function test_orders_index_includes_search_fields(): void
    {
        $user = User::factory()->create();
        $brand = Brand::create([
            'name' => 'Acme',
            'slug' => 'acme-'.Str::random(6),
            'description' => null,
            'image_path' => 'brands/acme.png',
            'status' => BrandStatus::Published,
            'featured' => false,
        ]);

        $variant = $this->createProductVariant(
            quantity: 1,
            price: '45.00',
            brand: $brand,
        );
        $order = Order::factory()->for($user)->create([
            'status' => OrderStatus::PENDING,
            'payment_status' => PaymentStatus::PENDING,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_variant_id' => $variant->id,
            'product_id' => $variant->product_id,
            'product_variant_snapshot' => OrderItem::createVariantSnapshot($variant),
            'price' => '45.00',
            'quantity' => 1,
            'discount_amount' => '0.00',
        ]);

        $response = $this->actingAs($user)->get(route('store.account.orders.index'));

        $response->assertInertia(
            fn ($page) => $page
                ->component('store/account/orders/index')
                ->has('orders', 1)
                ->where(
                    'orders.0.searchText',
                    fn (string $value) => str_contains($value, 'test product')
                        && str_contains($value, 'electronics')
                        && str_contains($value, 'acme')
                )
                ->where('orders.0.createdAtIso', $order->created_at->toDateString())
        );
    }

    private function createProductVariant(int $quantity, string $price, ?Brand $brand = null): ProductVariant
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
            'brand_id' => $brand?->id,
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
