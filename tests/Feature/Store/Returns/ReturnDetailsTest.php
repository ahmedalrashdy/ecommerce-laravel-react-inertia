<?php

namespace Tests\Feature\Store\Returns;

use App\Enums\CategoryStatus;
use App\Enums\ItemCondition;
use App\Enums\OrderStatus;
use App\Enums\ProductStatus;
use App\Enums\ReturnResolution;
use App\Enums\ReturnStatus;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ReturnHistory;
use App\Models\ReturnItem;
use App\Models\ReturnItemInspection;
use App\Models\ReturnOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ReturnDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_return_details_page(): void
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
            'created_at' => now()->subDays(3),
        ]);

        $returnOrder = ReturnOrder::factory()->create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'status' => ReturnStatus::INSPECTED,
            'refund_amount' => '120.00',
        ]);

        $returnItem = ReturnItem::create([
            'return_id' => $returnOrder->id,
            'order_item_id' => $orderItem->id,
            'quantity' => 1,
            'reason' => 'المقاس غير مناسب',
        ]);

        ReturnItemInspection::create([
            'return_item_id' => $returnItem->id,
            'condition' => ItemCondition::OPEN_BOX,
            'quantity' => 1,
            'resolution' => ReturnResolution::REFUND,
            'note' => 'تم الفحص',
            'refund_amount' => '120.00',
        ]);

        ReturnHistory::create([
            'return_id' => $returnOrder->id,
            'status' => ReturnStatus::INSPECTED,
            'comment' => 'تم فحص المرتجع',
            'is_visible_to_user' => true,
        ]);

        $response = $this->actingAs($user)->get(route('store.returns.show', $returnOrder));

        $response->assertOk();

        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('store/returns/show')
                ->where('returnOrder.returnNumber', $returnOrder->return_number)
                ->has('returnOrder.items', 1)
                ->has('returnOrder.timeline', 1)
        );
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
