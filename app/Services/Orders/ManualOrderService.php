<?php

namespace App\Services\Orders;

use App\Data\Payments\TransactionEntryData;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\StockMovementType;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\UserAddress;
use App\Services\Inventory\InventoryService;
use App\Services\Payments\TransactionService;
use Brick\Money\Money;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class ManualOrderService
{
    public function __construct(
        protected InventoryService $inventoryService,
        protected TransactionService $transactionService
    ) {}

    public function create(array $payload, User $actor): Order
    {
        $items = collect($payload['items'] ?? []);
        $transaction = $payload['transaction'] ?? null;

        if ($items->isEmpty()) {
            throw new RuntimeException(__('validation.manual_order_items_required'));
        }

        if (! $transaction) {
            throw new RuntimeException(__('validation.manual_transactions_required'));
        }

        $variants = $this->loadVariants($items);
        $paymentMethod = PaymentMethod::BANK_TRANSFER;

        foreach ($items as $item) {
            $variant = $variants->get($item['product_variant_id']);
            $quantity = (int) $item['quantity'];

            if (! $variant || $quantity <= 0 || $variant->quantity < $quantity) {
                throw new RuntimeException(__('validation.insufficient_stock_for_variant', [
                    'sku' => $variant?->sku ?? 'N/A',
                    'available' => $variant?->quantity ?? 0,
                ]));
            }
        }

        $discountAmount = Money::of((string) ($payload['discount_amount'] ?? 0), 'USD');
        $taxAmount = Money::of((string) ($payload['tax_amount'] ?? 0), 'USD');
        $shippingCost = Money::of((string) ($payload['shipping_cost'] ?? 0), 'USD');

        $subtotal = $items->reduce(function (Money $carry, array $item) use ($variants): Money {
            $variant = $variants->get($item['product_variant_id']);
            $unitPrice = Money::of((string) ($item['unit_price'] ?? $variant->price), 'USD');
            $quantity = (int) $item['quantity'];
            $itemDiscount = Money::of((string) ($item['discount_amount'] ?? 0), 'USD');
            $lineTotal = $unitPrice->multipliedBy($quantity);

            if ($itemDiscount->isGreaterThan($lineTotal)) {
                throw new RuntimeException(__('validation.manual_order_invalid_item_discount'));
            }

            return $carry->plus($lineTotal->minus($itemDiscount));
        }, Money::zero('USD'));

        if ($discountAmount->isGreaterThan($subtotal)) {
            throw new RuntimeException(__('validation.manual_order_invalid_discount'));
        }

        $grandTotal = $subtotal->minus($discountAmount)->plus($taxAmount)->plus($shippingCost);
        $grandTotal = $grandTotal->isNegative() ? Money::zero('USD') : $grandTotal;

        $shippingAddress = $this->resolveAddressSnapshot(
            $payload['shipping_address_id'] ?? null,
            __('validation.manual_order_shipping_address_required')
        );

        return DB::transaction(function () use (
            $payload,
            $actor,
            $items,
            $variants,
            $transaction,
            $paymentMethod,
            $subtotal,
            $discountAmount,
            $taxAmount,
            $shippingCost,
            $grandTotal,
            $shippingAddress
        ): Order {
            $paymentStatus = $this->resolvePaymentStatus($transaction);
            $orderStatus = OrderStatus::PROCESSING;

            $order = Order::create([
                'user_id' => $payload['user_id'],
                'order_number' => 'ORD-'.strtoupper(Str::random(10)),
                'type' => OrderType::NORMAL,
                'status' => $orderStatus,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'shipping_address_snapshot' => $shippingAddress,
                'subtotal' => $subtotal->getAmount()->__toString(),
                'discount_amount' => $discountAmount->getAmount()->__toString(),
                'tax_amount' => $taxAmount->getAmount()->__toString(),
                'shipping_cost' => $shippingCost->getAmount()->__toString(),
                'grand_total' => $grandTotal->getAmount()->__toString(),
                'paid_at' => $paymentStatus === PaymentStatus::PAID ? now() : null,
                'notes' => $payload['notes'] ?? null,
            ]);

            foreach ($items as $item) {
                $variant = $variants->get($item['product_variant_id']);

                $order->items()->create([
                    'product_variant_id' => $variant->id,
                    'product_id' => $variant->product_id,
                    'product_variant_snapshot' => OrderItem::createVariantSnapshot($variant),
                    'price' => (string) ($item['unit_price'] ?? $variant->price),
                    'quantity' => (int) $item['quantity'],
                    'discount_amount' => (string) ($item['discount_amount'] ?? '0.00'),
                ]);
            }

            $transactionStatus = TransactionStatus::Success;
            $transactionPaymentMethod = PaymentMethod::BANK_TRANSFER;

            $this->transactionService->record(new TransactionEntryData(
                order_id: $order->id,
                user_id: $actor->id,
                type: TransactionType::Payment,
                payment_method: $transactionPaymentMethod,
                amount: (float) $grandTotal->getAmount()->__toString(),
                currency: 'USD',
                status: $transactionStatus,
                transaction_ref: $transaction['transaction_ref'] ?? null,
                gateway_response: [],
                description: $transaction['note'] ?? __('filament.orders.manual_payment_recorded')
            ));

            foreach ($items as $item) {
                $variant = $variants->get($item['product_variant_id']);
                $quantity = (int) $item['quantity'];

                $this->inventoryService->decreaseStock(
                    $variant,
                    $quantity,
                    StockMovementType::SALE,
                    $order,
                    "Manual order #{$order->order_number}"
                );
            }

            $order->history()->create([
                'status' => $orderStatus,
                'comment' => __('filament.orders.manual_order_created'),
                'is_visible_to_user' => false,
                'actor_type' => $actor->getMorphClass(),
                'actor_id' => $actor->getKey(),
            ]);

            return $order;
        });
    }

    private function loadVariants(Collection $items): Collection
    {
        $variantIds = $items->pluck('product_variant_id')->unique()->values();

        $variants = ProductVariant::query()
            ->with(['product', 'attributeValues.attribute', 'defaultImage'])
            ->whereIn('id', $variantIds)
            ->get()
            ->keyBy('id');

        if ($variants->count() !== $variantIds->count()) {
            throw new RuntimeException(__('validation.manual_order_invalid_variants'));
        }

        return $variants;
    }

    private function resolvePaymentStatus(array $transaction): PaymentStatus
    {
        if (empty($transaction['transaction_ref'])) {
            throw new RuntimeException(__('validation.manual_transaction_reference_required'));
        }

        return PaymentStatus::PAID;
    }

    public static function calculateSubtotal(array $items): Money
    {
        $subtotal = Money::zero('USD');

        foreach ($items as $item) {
            $unitPrice = (float) ($item['unit_price'] ?? 0);
            $quantity = (int) ($item['quantity'] ?? 0);
            $discount = (float) ($item['discount_amount'] ?? 0);
            $lineTotal = Money::of($unitPrice, 'USD')
                ->multipliedBy($quantity)
                ->minus(Money::of($discount, 'USD'));

            if ($lineTotal->isNegative()) {
                $lineTotal = Money::zero('USD');
            }

            $subtotal = $subtotal->plus($lineTotal);
        }

        return $subtotal;
    }

    public static function calculateGrandTotal(array $items, float $discount, float $tax, float $shipping): Money
    {
        $subtotal = self::calculateSubtotal($items);
        $discountMoney = Money::of($discount, 'USD');
        $taxMoney = Money::of($tax, 'USD');
        $shippingMoney = Money::of($shipping, 'USD');

        $total = $subtotal->minus($discountMoney)->plus($taxMoney)->plus($shippingMoney);

        if ($total->isNegative()) {
            $total = Money::zero('USD');
        }

        return $total;
    }

    public static function formatMoney(Money $money): string
    {
        return \App\Data\Casts\MoneyCast::formatMoney($money);
    }

    public static function addressOptions(?int $userId): array
    {
        if (! $userId) {
            return [];
        }

        return UserAddress::query()
            ->where('user_id', $userId)
            ->orderByDesc('is_default_shipping')
            ->orderByDesc('id')
            ->get()
            ->mapWithKeys(function (UserAddress $address): array {
                $label = trim(implode(' - ', array_filter([
                    $address->contact_person,
                    $address->city,
                    $address->address_line_1,
                ])));

                return [$address->id => $label ?: (string) $address->id];
            })
            ->all();
    }

    public static function defaultShippingAddress(int $userId): ?UserAddress
    {
        return UserAddress::query()
            ->where('user_id', $userId)
            ->orderByDesc('is_default_shipping')
            ->orderByDesc('id')
            ->first();
    }

    private function resolveAddressSnapshot(?int $addressId, string $errorMessage): array
    {
        if (! $addressId) {
            throw new RuntimeException($errorMessage);
        }

        $address = \App\Models\UserAddress::find($addressId);

        if (! $address) {
            throw new RuntimeException($errorMessage);
        }

        return [
            'contact_person' => $address->contact_person ?? '',
            'contact_phone' => $address->contact_phone ?? '',
            'address_line_1' => $address->address_line_1 ?? '',
            'address_line_2' => $address->address_line_2 ?? null,
            'city' => $address->city ?? null,
            'state' => $address->state ?? null,
            'postal_code' => $address->postal_code ?? null,
            'country' => $address->country ?? null,
        ];
    }
}
