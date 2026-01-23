<?php

namespace Tests\Feature\Orders;

use App\Enums\CategoryStatus;
use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\UserAddress;
use App\Services\Orders\ManualOrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use RuntimeException;
use Tests\TestCase;

class ManualOrderServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_manual_order_fails_when_stock_is_insufficient(): void
    {
        $user = User::factory()->create();
        $variant = $this->createProductVariant(quantity: 0, price: '120.00');
        $address = $this->createAddress($user);

        $payload = [
            'user_id' => $user->id,
            'shipping_address_id' => $address->id,
            'items' => [
                [
                    'product_variant_id' => $variant->id,
                    'unit_price' => 120.0,
                    'quantity' => 1,
                    'discount_amount' => 0,
                ],
            ],
            'transaction' => [
                'transaction_ref' => 'MAN-REF-1',
                'note' => 'Bank transfer',
            ],
            'discount_amount' => 0,
            'tax_amount' => 0,
            'shipping_cost' => 0,
        ];

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(__('validation.insufficient_stock_for_variant', [
            'sku' => $variant->sku,
            'available' => 0,
        ]));

        app(ManualOrderService::class)->create($payload, $user);
    }

    public function test_manual_order_requires_shipping_address(): void
    {
        $user = User::factory()->create();
        $variant = $this->createProductVariant(quantity: 3, price: '120.00');

        $payload = [
            'user_id' => $user->id,
            'items' => [
                [
                    'product_variant_id' => $variant->id,
                    'unit_price' => 120.0,
                    'quantity' => 1,
                    'discount_amount' => 0,
                ],
            ],
            'transaction' => [
                'transaction_ref' => 'MAN-REF-1',
                'note' => 'Bank transfer',
            ],
            'discount_amount' => 0,
            'tax_amount' => 0,
            'shipping_cost' => 0,
        ];

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(__('validation.manual_order_shipping_address_required'));

        app(ManualOrderService::class)->create($payload, $user);
    }

    public function test_manual_order_uses_shipping_address_snapshot(): void
    {
        $user = User::factory()->create();
        $variant = $this->createProductVariant(quantity: 3, price: '120.00');
        $address = $this->createAddress($user);

        $payload = [
            'user_id' => $user->id,
            'shipping_address_id' => $address->id,
            'items' => [
                [
                    'product_variant_id' => $variant->id,
                    'unit_price' => 120.0,
                    'quantity' => 1,
                    'discount_amount' => 0,
                ],
            ],
            'transaction' => [
                'transaction_ref' => 'MAN-REF-1',
                'note' => 'Bank transfer',
            ],
            'discount_amount' => 0,
            'tax_amount' => 0,
            'shipping_cost' => 0,
        ];

        $order = app(ManualOrderService::class)->create($payload, $user);

        $this->assertSame($address->contact_person, $order->shipping_address_snapshot['contact_person']);
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

    private function createAddress(User $user): UserAddress
    {
        return UserAddress::create([
            'user_id' => $user->id,
            'contact_person' => 'Ahmed',
            'contact_phone' => '0500000000',
            'address_line_1' => 'Street 1',
            'address_line_2' => null,
            'city' => 'Riyadh',
            'state' => 'Riyadh',
            'postal_code' => '12345',
            'country' => 'SA',
            'is_default_shipping' => true,
        ]);
    }
}
