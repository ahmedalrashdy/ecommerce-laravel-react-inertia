<?php

namespace Tests\Feature\Store\Orders;

use App\Enums\CategoryStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ProductStatus;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CheckoutFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_page_loads_with_summary_and_addresses(): void
    {
        $user = User::factory()->create();
        $variant = $this->createProductVariant(quantity: 10, price: '100.00');
        $cart = Cart::create(['user_id' => $user->id]);
        $cart->items()->create([
            'product_variant_id' => $variant->id,
            'quantity' => 2,
            'is_selected' => true,
        ]);
        $address = $this->createAddress($user, true);

        $response = $this->actingAs($user)->get(route('store.checkout.index'));

        $response->assertStatus(200);
        $response->assertInertia(
            fn ($page) => $page
                ->component('store/checkout/index')
                ->has('items', 1)
                ->has('addresses', 1)
                ->where('defaultShippingAddressId', $address->id)
                ->has('idempotencyKey')
                ->where('summary.subtotal', '200.00')
                ->where('summary.shippingCost', '50.00')
                ->where('summary.taxAmount', '30.00')
                ->where('summary.grandTotal', '280.00')
        );
    }

    public function test_user_can_place_order_from_selected_items(): void
    {
        $user = User::factory()->create();
        $variant = $this->createProductVariant(quantity: 10, price: '100.00');
        $cart = Cart::create(['user_id' => $user->id]);
        $cart->items()->create([
            'product_variant_id' => $variant->id,
            'quantity' => 2,
            'is_selected' => true,
        ]);
        $address = $this->createAddress($user, true);
        $idempotencyKey = (string) Str::uuid();

        $response = $this->actingAs($user)
            ->withHeaders(['X-Idempotency-Key' => $idempotencyKey])
            ->post(route('store.checkout.place-order'), [
                'shipping_address_id' => $address->id,
                'selected_items' => [$variant->id],
            ]);

        $order = Order::where('user_id', $user->id)->first();
        $this->assertNotNull($order);

        $response->assertRedirect(route('store.payments.start', $order));
        $this->assertSame(OrderStatus::PENDING, $order->status);
        $this->assertSame(PaymentStatus::PENDING, $order->payment_status);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_variant_id' => $variant->id,
            'quantity' => 2,
        ]);

        $variant->refresh();
        $this->assertSame(8, $variant->quantity);

        $this->assertDatabaseMissing('cart_items', [
            'cart_id' => $cart->id,
            'product_variant_id' => $variant->id,
        ]);

        $this->assertDatabaseHas('order_histories', [
            'order_id' => $order->id,
            'status' => OrderStatus::PENDING->value,
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'idempotency_key' => $idempotencyKey,
        ]);
    }

    public function test_place_order_requires_selected_items_in_cart(): void
    {
        $user = User::factory()->create();
        $variant = $this->createProductVariant(quantity: 5, price: '75.00');
        $address = $this->createAddress($user);
        $idempotencyKey = (string) Str::uuid();

        $response = $this->actingAs($user)
            ->withHeaders(['X-Idempotency-Key' => $idempotencyKey])
            ->post(route('store.checkout.place-order'), [
                'shipping_address_id' => $address->id,
                'selected_items' => [$variant->id],
            ]);

        $response->assertSessionHasErrors('selected_items');
    }

    public function test_place_order_requires_idempotency_key_header(): void
    {
        $user = User::factory()->create();
        $variant = $this->createProductVariant(quantity: 5, price: '75.00');
        $cart = Cart::create(['user_id' => $user->id]);
        $cart->items()->create([
            'product_variant_id' => $variant->id,
            'quantity' => 1,
            'is_selected' => true,
        ]);
        $address = $this->createAddress($user);

        $response = $this->actingAs($user)
            ->from(route('store.checkout.index'))
            ->post(route('store.checkout.place-order'), [
                'shipping_address_id' => $address->id,
                'selected_items' => [$variant->id],
            ]);

        $response->assertRedirect(route('store.checkout.index'));
        $response->assertSessionHas('inertia.flash_data');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_place_order_is_idempotent_for_same_key(): void
    {
        $user = User::factory()->create();
        $variant = $this->createProductVariant(quantity: 10, price: '100.00');
        $cart = Cart::create(['user_id' => $user->id]);
        $cart->items()->create([
            'product_variant_id' => $variant->id,
            'quantity' => 2,
            'is_selected' => true,
        ]);
        $address = $this->createAddress($user, true);
        $idempotencyKey = (string) Str::uuid();

        $firstResponse = $this->actingAs($user)
            ->withHeaders(['X-Idempotency-Key' => $idempotencyKey])
            ->post(route('store.checkout.place-order'), [
                'shipping_address_id' => $address->id,
                'selected_items' => [$variant->id],
            ]);

        $order = Order::where('user_id', $user->id)->first();
        $this->assertNotNull($order);
        $firstResponse->assertRedirect(route('store.payments.start', $order));

        $secondResponse = $this->actingAs($user)
            ->withHeaders(['X-Idempotency-Key' => $idempotencyKey])
            ->post(route('store.checkout.place-order'), [
                'shipping_address_id' => $address->id,
                'selected_items' => [$variant->id],
            ]);

        $secondResponse->assertRedirect(route('store.payments.start', $order));
        $this->assertDatabaseCount('orders', 1);
    }

    private function createCategory(): Category
    {
        return Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics-'.Str::random(6),
            'description' => null,
            'image_path' => 'categories/electronics.png',
            'status' => CategoryStatus::Published,
            'products_count' => 0,
            '_lft' => 1,
            '_rgt' => 2,
        ]);
    }

    private function createProductVariant(int $quantity, string $price): ProductVariant
    {
        $category = $this->createCategory();
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

    private function createAddress(User $user, bool $isDefaultShipping = false): UserAddress
    {
        return UserAddress::create([
            'user_id' => $user->id,
            'contact_person' => 'Client',
            'contact_phone' => '0500000000',
            'address_line_1' => 'Riyadh',
            'address_line_2' => null,
            'city' => 'Riyadh',
            'state' => null,
            'country' => 'SA',
            'postal_code' => null,
            'is_default_shipping' => $isDefaultShipping,
        ]);
    }
}
