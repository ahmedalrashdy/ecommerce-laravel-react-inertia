<?php

namespace Tests\Feature\Returns;

use App\Data\Returns\AdminReturnCreationData;
use App\Enums\OrderStatus;
use App\Enums\ReturnStatus;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\Returns\AdminReturnCreationService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminReturnCreationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_requires_delivered_order(): void
    {
        $admin = User::factory()->create();
        $order = Order::factory()->create(['status' => OrderStatus::PROCESSING]);
        $variant = $this->createProductVariant(quantity: 3, price: '120.00');

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_variant_id' => $variant->id,
            'product_id' => $variant->product_id,
            'product_variant_snapshot' => OrderItem::createVariantSnapshot($variant),
            'price' => '120.00',
            'quantity' => 1,
            'discount_amount' => '0.00',
        ]);

        $data = new AdminReturnCreationData(
            orderId: $order->id,
            status: ReturnStatus::REQUESTED,
            reason: 'Damaged',
            items: collect([
                [
                    'order_item_id' => $orderItem->id,
                    'quantity' => 1,
                    'reason' => 'Damaged',
                ],
            ]),
        );

        $this->expectException(Exception::class);

        app(AdminReturnCreationService::class)->create($data, $admin);
    }

    private function createProductVariant(int $quantity, string $price): ProductVariant
    {
        $category = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics-'.Str::random(6),
            'description' => null,
            'image_path' => 'categories/electronics.png',
            'status' => \App\Enums\CategoryStatus::Published,
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
            'status' => \App\Enums\ProductStatus::Published,
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
