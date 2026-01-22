<?php

namespace Database\Factories;

use App\Enums\CategoryStatus;
use App\Enums\ItemCondition;
use App\Enums\ProductStatus;
use App\Enums\ReturnResolution;
use App\Enums\ReturnStatus;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ReturnItem;
use App\Models\ReturnOrder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReturnItemInspection>
 */
class ReturnItemInspectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $category = Category::create([
            'name' => $this->faker->words(2, true),
            'slug' => 'category-'.Str::random(10),
            'description' => $this->faker->sentence(),
            'image_path' => null,
            'status' => CategoryStatus::Published,
            'products_count' => 0,
            '_lft' => 1,
            '_rgt' => 2,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'brand_id' => null,
            'name' => $this->faker->words(2, true),
            'slug' => 'product-'.Str::random(10),
            'description' => null,
            'specifications' => null,
            'status' => ProductStatus::Published,
            'featured' => false,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'SKU-'.Str::random(8),
            'price' => 120.00,
            'compare_at_price' => null,
            'quantity' => 10,
            'is_default' => true,
        ]);

        $order = Order::factory()->create();

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_variant_id' => $variant->id,
            'product_id' => $variant->product_id,
            'product_variant_snapshot' => OrderItem::createVariantSnapshot($variant),
            'price' => $variant->price,
            'quantity' => 2,
            'discount_amount' => '0.00',
        ]);

        $returnOrder = ReturnOrder::factory()->create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'status' => ReturnStatus::RECEIVED,
        ]);

        $returnItem = ReturnItem::create([
            'return_id' => $returnOrder->id,
            'order_item_id' => $orderItem->id,
            'quantity' => 2,
            'reason' => $this->faker->sentence(),
        ]);

        return [
            'return_item_id' => $returnItem->id,
            'condition' => ItemCondition::SEALED,
            'quantity' => 1,
            'resolution' => ReturnResolution::REFUND,
            'note' => $this->faker->sentence(),
            'refund_amount' => '120.00',
        ];
    }
}
