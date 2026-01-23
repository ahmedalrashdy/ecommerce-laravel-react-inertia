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
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class OrderReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_review_for_delivered_order(): void
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

        $response = $this->actingAs($user)->post(route('store.orders.reviews', $order), [
            'product_id' => $variant->product_id,
            'rating' => 4,
            'comment' => 'منتج ممتاز',
        ]);

        $response->assertRedirect(route('store.orders.show', $order));

        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'product_id' => $variant->product_id,
            'rating' => 4,
            'comment' => 'منتج ممتاز',
            'is_approved' => false,
        ]);
    }

    public function test_user_can_update_existing_review(): void
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

        Review::create([
            'user_id' => $user->id,
            'product_id' => $variant->product_id,
            'rating' => 2,
            'comment' => 'قديم',
            'is_approved' => false,
        ]);

        $this->actingAs($user)->post(route('store.orders.reviews', $order), [
            'product_id' => $variant->product_id,
            'rating' => 5,
            'comment' => 'تم التحديث',
        ]);

        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'product_id' => $variant->product_id,
            'rating' => 5,
            'comment' => 'تم التحديث',
        ]);
    }

    public function test_user_cannot_review_before_delivery(): void
    {
        $user = User::factory()->create();
        $variant = $this->createProductVariant(quantity: 5, price: '120.00');
        $order = Order::factory()->for($user)->create([
            'status' => OrderStatus::PROCESSING,
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

        $response = $this->actingAs($user)->post(route('store.orders.reviews', $order), [
            'product_id' => $variant->product_id,
            'rating' => 5,
            'comment' => 'محاولة',
        ]);

        $response->assertStatus(403);
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
