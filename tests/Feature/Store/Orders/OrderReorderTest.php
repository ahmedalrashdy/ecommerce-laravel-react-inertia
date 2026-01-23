<?php

namespace Tests\Feature\Store\Orders;

use App\Enums\CategoryStatus;
use App\Enums\ProductStatus;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class OrderReorderTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_reorder_items_into_cart(): void
    {
        $user = User::factory()->create();
        $variant = $this->createProductVariant(quantity: 10, price: '55.00');
        $order = Order::factory()->for($user)->create();

        OrderItem::create([
            'order_id' => $order->id,
            'product_variant_id' => $variant->id,
            'product_id' => $variant->product_id,
            'product_variant_snapshot' => OrderItem::createVariantSnapshot($variant),
            'price' => '55.00',
            'quantity' => 3,
            'discount_amount' => '0.00',
        ]);

        $response = $this->actingAs($user)->post(route('store.orders.reorder', $order));

        $response->assertRedirect(route('store.cart.index'));

        $cart = Cart::where('user_id', $user->id)->first();
        $this->assertNotNull($cart);

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->id,
            'product_variant_id' => $variant->id,
            'quantity' => 3,
            'is_selected' => true,
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
