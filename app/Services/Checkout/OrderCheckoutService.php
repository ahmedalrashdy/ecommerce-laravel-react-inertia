<?php

namespace App\Services\Checkout;

use App\Data\Orders\CheckoutData;
use App\Data\Orders\OrderCalculationData;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\PaymentStatus;
use App\Enums\StockMovementType;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Services\Inventory\InventoryService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderCheckoutService
{
    public function __construct(
        protected InventoryService $inventoryService,
        protected PricingService $pricingService
    ) {}

    public function process(CheckoutData $data): Order
    {
        $variantIds = $data->items->pluck('product_variant_id')->all();
        $variants = ProductVariant::query()
            ->with(['product', 'attributeValues.attribute', 'defaultImage'])
            ->whereIn('id', $variantIds)
            ->get()
            ->keyBy('id');

        if ($variants->count() !== count($variantIds)) {
            throw new Exception('بعض المنتجات لم تعد متاحة.');
        }

        $calculations = $this->pricingService->calculateTotal($data->items, $data->shippingAddress);

        return DB::transaction(function () use ($data, $calculations, $variants, $variantIds) {
            $order = $this->createOrderRecord($data, $calculations);

            foreach ($data->items as $itemData) {
                $variant = $variants->get($itemData->product_variant_id);

                if (! $variant) {
                    throw new Exception('بعض المنتجات لم تعد متاحة.');
                }

                $order->items()->create([
                    'product_variant_id' => $variant->id,
                    'product_id' => $variant->product_id,
                    'product_variant_snapshot' => OrderItem::createVariantSnapshot($variant),
                    'price' => $itemData->unit_price,
                    'quantity' => $itemData->quantity,
                    'discount_amount' => '0.00',
                ]);

                $this->inventoryService->decreaseStock(
                    $variant,
                    $itemData->quantity,
                    StockMovementType::SALE,
                    $order,
                    "Order #{$order->order_number}"
                );
            }

            $order->history()->create([
                'status' => OrderStatus::PENDING,
                'comment' => 'Order placed successfully',
                'is_visible_to_user' => true,
                'actor_type' => get_class($data->user),
                'actor_id' => $data->user->id,
            ]);

            $this->clearPurchasedItemsFromCart($data->user->id, $variantIds);

            return $order;
        });
    }

    protected function createOrderRecord(CheckoutData $data, OrderCalculationData $calc): Order
    {
        return Order::create([
            'user_id' => $data->user->id,
            'order_number' => 'ORD-'.strtoupper(Str::random(10)),
            'type' => OrderType::NORMAL,
            'status' => OrderStatus::PENDING,
            'payment_method' => $data->paymentMethod,
            'payment_status' => PaymentStatus::PENDING,
            'idempotency_key' => $data->idempotencyKey,

            'shipping_address_snapshot' => $data->shippingAddress->toArray(),

            'subtotal' => $calc->subtotal,
            'tax_amount' => $calc->tax_amount,
            'shipping_cost' => $calc->shipping_cost,
            'discount_amount' => $calc->discount_amount,
            'grand_total' => $calc->grand_total,

            'notes' => $data->notes,
        ]);
    }

    protected function clearPurchasedItemsFromCart(int $userId, array $variantIds): void
    {
        $cart = Cart::where('user_id', $userId)->first();
        if (! $cart) {
            return;
        }

        $cart->items()
            ->whereIn('product_variant_id', $variantIds)
            ->delete();
    }
}
