<?php

namespace App\Services\Returns;

use App\Data\Payments\TransactionEntryData;
use App\Enums\ItemCondition;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\PaymentStatus;
use App\Enums\ReturnResolution;
use App\Enums\ReturnStatus;
use App\Enums\StockMovementType;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\ReturnItem;
use App\Models\ReturnItemInspection;
use App\Models\ReturnOrder;
use App\Models\User;
use App\Services\Inventory\InventoryService;
use App\Services\Payments\TransactionService;
use Brick\Money\Money;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReturnInspectionWorkflowService
{
    public function __construct(
        protected InventoryService $inventoryService,
        protected TransactionService $transactionService
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function process(ReturnOrder $returnOrder, array $data, User $adminUser): ReturnOrder
    {
        if ($returnOrder->status !== ReturnStatus::RECEIVED) {
            throw new Exception('يجب استلام الشحنة أولاً قبل الفحص.');
        }

        $items = $data['items'] ?? [];
        $replacementItems = $data['replacement_items'] ?? [];
        $transactionReference = $data['transaction_reference'] ?? null;

        if ($items === []) {
            throw new Exception('يجب إدخال نتائج الفحص قبل المتابعة.');
        }

        return DB::transaction(function () use ($returnOrder, $items, $replacementItems, $transactionReference, $adminUser): ReturnOrder {
            $returnOrder->load('items.orderItem.productVariant.product', 'order');

            $inspections = $this->collectInspections($returnOrder, $items);
            $this->validateInspectionQuantities($returnOrder, $inspections);
            $this->validateInspectionRules($inspections);

            $returnItemIds = $inspections->pluck('return_item_id')->unique()->all();
            ReturnItemInspection::query()
                ->whereIn('return_item_id', $returnItemIds)
                ->delete();

            foreach ($inspections as $inspection) {
                ReturnItemInspection::create([
                    'return_item_id' => $inspection['return_item_id'],
                    'condition' => $inspection['condition'],
                    'quantity' => $inspection['quantity'],
                    'resolution' => $inspection['resolution'],
                    'note' => $inspection['note'],
                    'refund_amount' => $inspection['refund_amount'],
                ]);
            }

            $returnOrder->update([
                'status' => ReturnStatus::INSPECTED,
                'inspected_at' => now(),
                'inspected_by' => $adminUser->id,
            ]);

            $returnOrder->history()->create([
                'status' => ReturnStatus::INSPECTED,
                'comment' => 'تم الفحص وتحديد القرارات',
                'actor_type' => $adminUser->getMorphClass(),
                'actor_id' => $adminUser->getKey(),
            ]);

            $hasReplacement = $inspections->contains(fn (array $inspection): bool => $inspection['resolution'] === ReturnResolution::REPLACEMENT);
            $hasRejected = $inspections->contains(fn (array $inspection): bool => $inspection['resolution'] === ReturnResolution::REJECT);

            if ($hasReplacement && $replacementItems === []) {
                $replacementItems = $this->replacementItemsFromInspections($inspections);
            }

            if ($hasReplacement && $replacementItems === []) {
                throw new Exception('يجب تحديد عناصر الاستبدال قبل المتابعة.');
            }

            if ($hasReplacement) {
                $this->createReplacementOrder($returnOrder, $replacementItems, $adminUser);
            }

            if ($hasRejected) {
                $this->createReshipmentOrder($returnOrder, $inspections, $adminUser);
            }

            $this->handleStockMovements($returnOrder, $inspections);

            $refundTotal = $this->refundTotal($inspections);
            $replacementTotal = $this->replacementTotal($replacementItems);
            $difference = $replacementTotal->minus($refundTotal);
            $hasDifference = ! $difference->isZero();
            $shouldRefund = $difference->isZero() && $refundTotal->isGreaterThan(Money::zero('USD'));

            if ($hasDifference || $shouldRefund) {
                if (! $transactionReference) {
                    throw new Exception('رقم المرجع للمعاملة مطلوب لإكمال العملية.');
                }

                $this->recordTransaction(
                    $returnOrder,
                    $shouldRefund ? $refundTotal->negated() : $difference,
                    $transactionReference,
                    $adminUser
                );
            }

            if ($refundTotal->isGreaterThan(Money::zero('USD'))) {
                $returnOrder->update([
                    'refund_amount' => $refundTotal->getAmount()->__toString(),
                ]);
            }

            $returnOrder->update([
                'status' => ReturnStatus::COMPLETED,
            ]);

            $returnOrder->history()->create([
                'status' => ReturnStatus::COMPLETED,
                'comment' => 'تم تنفيذ المرتجع وإغلاق الملف',
                'actor_type' => $adminUser->getMorphClass(),
                'actor_id' => $adminUser->getKey(),
            ]);

            $this->updateOrderStatus($returnOrder, $refundTotal);

            return $returnOrder;
        });
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function collectInspections(ReturnOrder $returnOrder, array $items): Collection
    {
        $returnItems = $returnOrder->items->keyBy('id');

        $lines = collect($items)->flatMap(function (array $item) use ($returnItems): array {
            $returnItemId = (int) ($item['return_item_id'] ?? 0);
            /** @var ReturnItem|null $returnItem */
            $returnItem = $returnItems->get($returnItemId);
            if (! $returnItem) {
                throw new Exception('عنصر المرتجع غير صالح.');
            }

            $unitPrice = (float) ($item['unit_price'] ?? $returnItem->orderItem->price);

            return collect($item['inspections'] ?? [])
                ->map(function (array $inspection) use ($returnItem, $unitPrice): array {
                    $refundAmount = $inspection['refund_amount'] ?? null;
                    $resolutionValue = (int) ($inspection['resolution'] ?? 0);
                    $shouldCalculateRefund = in_array($resolutionValue, [
                        ReturnResolution::REFUND->value,
                        ReturnResolution::REPLACEMENT->value,
                    ], true);

                    if ($refundAmount === null && $shouldCalculateRefund) {
                        $refundAmount = $unitPrice * (int) ($inspection['quantity'] ?? 0);
                    }

                    $conditionValue = $inspection['condition'] ?? null;
                    $resolutionValue = $inspection['resolution'] ?? null;

                    if ($conditionValue === null || $resolutionValue === null) {
                        throw new Exception('يجب تحديد الحالة والقرار لكل عنصر.');
                    }

                    return [
                        'return_item_id' => $returnItem->id,
                        'return_quantity' => $returnItem->quantity,
                        'order_item' => $returnItem->orderItem,
                        'condition' => ItemCondition::from((int) $conditionValue),
                        'resolution' => ReturnResolution::from((int) $resolutionValue),
                        'quantity' => (int) $inspection['quantity'],
                        'note' => $inspection['note'] ?? null,
                        'refund_amount' => $refundAmount !== null ? (float) $refundAmount : null,
                    ];
                })
                ->all();
        });

        return $lines
            ->groupBy(fn (array $inspection): string => $inspection['return_item_id'].'-'.$inspection['condition']->value.'-'.$inspection['resolution']->value)
            ->map(function (Collection $group): array {
                $first = $group->first();

                return [
                    'return_item_id' => $first['return_item_id'],
                    'return_quantity' => $first['return_quantity'],
                    'order_item' => $first['order_item'],
                    'condition' => $first['condition'],
                    'resolution' => $first['resolution'],
                    'quantity' => $group->sum('quantity'),
                    'note' => $group->pluck('note')->filter()->implode(' | ') ?: null,
                    'refund_amount' => in_array($first['resolution'], [
                        ReturnResolution::REFUND,
                        ReturnResolution::REPLACEMENT,
                    ], true)
                        ? $group->sum(fn (array $inspection): float => (float) ($inspection['refund_amount'] ?? 0))
                        : null,
                ];
            })
            ->values();
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $inspections
     */
    private function validateInspectionQuantities(ReturnOrder $returnOrder, Collection $inspections): void
    {
        $byReturnItem = $inspections->groupBy('return_item_id');

        foreach ($returnOrder->items as $returnItem) {
            $total = $byReturnItem->get($returnItem->id, collect())
                ->sum(fn (array $inspection): int => $inspection['quantity']);

            if ($total !== $returnItem->quantity) {
                throw new Exception("مجموع كميات الفحص للمنتج {$returnItem->orderItem->product_name} يجب أن يساوي {$returnItem->quantity}.");
            }
        }
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $inspections
     */
    private function validateInspectionRules(Collection $inspections): void
    {
        foreach ($inspections as $inspection) {
            if ($inspection['quantity'] < 0) {
                throw new Exception('الكمية يجب أن تكون صفر أو أكثر.');
            }

            if ($inspection['quantity'] > $inspection['return_quantity']) {
                throw new Exception('لا يمكن أن تتجاوز كمية الفحص كمية طلب الإرجاع.');
            }

            if (in_array($inspection['resolution'], [ReturnResolution::REFUND, ReturnResolution::REPLACEMENT], true)) {
                if (! in_array($inspection['condition'], [ItemCondition::SEALED, ItemCondition::OPEN_BOX, ItemCondition::DAMAGED], true)) {
                    throw new Exception('قيمة الاسترجاع مسموحة للحالة الجديدة أو المفتوحة السليمة أو التالفة.');
                }

                $maxRefund = (float) $inspection['order_item']->price * $inspection['quantity'];
                if ($inspection['refund_amount'] !== null && $inspection['refund_amount'] > $maxRefund) {
                    throw new Exception('مبلغ الاسترجاع لا يمكن أن يتجاوز القيمة الأصلية للمنتج.');
                }
            }

            if ($inspection['condition'] === ItemCondition::WRONG_ITEM && $inspection['resolution'] !== ReturnResolution::REJECT) {
                throw new Exception('الحالة منتج خطأ مسموح لها بالرفض فقط حالياً.');
            }

            if (in_array($inspection['resolution'], [ReturnResolution::REFUND, ReturnResolution::REPLACEMENT], true) && $inspection['refund_amount'] === null) {
                throw new Exception('مبلغ الاسترجاع مطلوب عند اختيار استبدال أو استرجاع المال.');
            }

            if ($inspection['refund_amount'] !== null && $inspection['refund_amount'] < 0) {
                throw new Exception('مبلغ الاسترجاع يجب أن يكون رقماً موجباً.');
            }
        }
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $inspections
     */
    private function refundTotal(Collection $inspections): Money
    {
        return $inspections
            ->filter(fn (array $inspection): bool => in_array($inspection['resolution'], [
                ReturnResolution::REFUND,
                ReturnResolution::REPLACEMENT,
            ], true))
            ->reduce(function (Money $carry, array $inspection): Money {
                $refundAmount = Money::of((string) ($inspection['refund_amount'] ?? 0), 'USD');

                return $carry->plus($refundAmount);
            }, Money::zero('USD'));
    }

    /**
     * @param  array<int, array<string, mixed>>  $replacementItems
     */
    private function replacementTotal(array $replacementItems): Money
    {
        return collect($replacementItems)
            ->reduce(function (Money $carry, array $item): Money {
                $unitPrice = Money::of((string) ($item['unit_price'] ?? 0), 'USD');
                $quantity = (int) ($item['quantity'] ?? 0);

                return $carry->plus($unitPrice->multipliedBy($quantity));
            }, Money::zero('USD'));
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $inspections
     * @return array<int, array<string, mixed>>
     */
    private function replacementItemsFromInspections(Collection $inspections): array
    {
        return $inspections
            ->filter(fn (array $inspection): bool => $inspection['resolution'] === ReturnResolution::REPLACEMENT)
            ->map(fn (array $inspection): array => [
                'product_variant_id' => $inspection['order_item']->product_variant_id,
                'quantity' => $inspection['quantity'],
                'unit_price' => (float) $inspection['order_item']->price,
            ])
            ->groupBy('product_variant_id')
            ->map(function (Collection $group): array {
                $first = $group->first();

                return [
                    'product_variant_id' => $first['product_variant_id'],
                    'quantity' => $group->sum('quantity'),
                    'unit_price' => $first['unit_price'],
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @param  array<int, array<string, mixed>>  $replacementItems
     */
    private function createReplacementOrder(ReturnOrder $returnOrder, array $replacementItems, User $adminUser): void
    {
        $order = $returnOrder->order;

        $replacementOrder = Order::create([
            'user_id' => $order->user_id,
            'parent_order_id' => $order->id,
            'type' => OrderType::REPLACEMENT,
            'order_number' => 'REP-'.$order->order_number,
            'status' => OrderStatus::PROCESSING,
            'payment_status' => PaymentStatus::PAID,
            'payment_method' => $order->payment_method,
            'shipping_address_snapshot' => $order->shipping_address_snapshot,
            'subtotal' => 0,
            'grand_total' => 0,
            'notes' => "استبدال للمرتجع رقم {$returnOrder->return_number}",
        ]);

        foreach ($replacementItems as $item) {
            $variant = ProductVariant::findOrFail($item['product_variant_id']);
            $quantity = (int) $item['quantity'];
            $unitPrice = (float) ($item['unit_price'] ?? $variant->price);

            $replacementOrder->items()->create([
                'product_variant_id' => $variant->id,
                'product_id' => $variant->product_id,
                'product_variant_snapshot' => OrderItem::createVariantSnapshot($variant),
                'price' => $unitPrice,
                'quantity' => $quantity,
                'discount_amount' => '0.00',
            ]);

            $this->inventoryService->decreaseStock(
                $variant,
                $quantity,
                StockMovementType::REPLACEMENT_OUT,
                $replacementOrder,
                "صرف استبدال للطلب #{$replacementOrder->order_number}"
            );
        }

        OrderHistory::create([
            'order_id' => $replacementOrder->id,
            'status' => $replacementOrder->status,
            'comment' => __('filament.orders.status_changed', [
                'status' => $replacementOrder->status->getLabel(),
            ]),
            'is_visible_to_user' => false,
            'actor_type' => $adminUser->getMorphClass(),
            'actor_id' => $adminUser->getKey(),
        ]);
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $inspections
     */
    private function createReshipmentOrder(ReturnOrder $returnOrder, Collection $inspections, User $adminUser): void
    {
        $order = $returnOrder->order;

        $reshipmentOrder = Order::create([
            'user_id' => $order->user_id,
            'parent_order_id' => $order->id,
            'type' => OrderType::RETURN_SHIPMENT,
            'order_number' => 'RSH-'.$order->order_number,
            'status' => OrderStatus::PROCESSING,
            'payment_status' => PaymentStatus::PAID,
            'payment_method' => $order->payment_method,
            'shipping_address_snapshot' => $order->shipping_address_snapshot,
            'subtotal' => 0,
            'grand_total' => 0,
            'notes' => "إعادة شحن لعناصر مرفوضة من المرتجع رقم {$returnOrder->return_number}",
        ]);

        $rejected = $inspections
            ->filter(fn (array $inspection): bool => $inspection['resolution'] === ReturnResolution::REJECT)
            ->groupBy(fn (array $inspection): int => $inspection['order_item']->product_variant_id);

        foreach ($rejected as $variantId => $group) {
            $variant = ProductVariant::findOrFail($variantId);
            $quantity = $group->sum('quantity');

            $reshipmentOrder->items()->create([
                'product_variant_id' => $variant->id,
                'product_id' => $variant->product_id,
                'product_variant_snapshot' => OrderItem::createVariantSnapshot($variant),
                'price' => 0,
                'quantity' => $quantity,
                'discount_amount' => '0.00',
            ]);
        }

        OrderHistory::create([
            'order_id' => $reshipmentOrder->id,
            'status' => $reshipmentOrder->status,
            'comment' => __('filament.orders.status_changed', [
                'status' => $reshipmentOrder->status->getLabel(),
            ]),
            'is_visible_to_user' => false,
            'actor_type' => $adminUser->getMorphClass(),
            'actor_id' => $adminUser->getKey(),
        ]);
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $inspections
     */
    private function handleStockMovements(ReturnOrder $returnOrder, Collection $inspections): void
    {
        foreach ($inspections as $inspection) {
            $orderItem = $inspection['order_item'];
            $variant = $orderItem->productVariant;
            if (! $variant) {
                continue;
            }

            if ($inspection['resolution'] === ReturnResolution::REFUND) {
                if (in_array($inspection['condition'], [ItemCondition::SEALED, ItemCondition::OPEN_BOX], true)) {
                    $this->inventoryService->increaseStock(
                        $variant,
                        $inspection['quantity'],
                        StockMovementType::RETURN_RESTOCK,
                        $returnOrder,
                        "استرجاع للمرتجع #{$returnOrder->return_number}"
                    );
                }

                if ($inspection['condition'] === ItemCondition::DAMAGED) {
                    $this->recordWasteMovement($variant, $inspection['quantity'], $returnOrder);
                }
            }

            if ($inspection['resolution'] === ReturnResolution::REPLACEMENT) {
                if (in_array($inspection['condition'], [ItemCondition::SEALED, ItemCondition::OPEN_BOX], true)) {
                    $this->inventoryService->increaseStock(
                        $variant,
                        $inspection['quantity'],
                        StockMovementType::RETURN_RESTOCK,
                        $returnOrder,
                        "استرجاع للمرتجع #{$returnOrder->return_number}"
                    );
                }

                if ($inspection['condition'] === ItemCondition::DAMAGED) {
                    $this->recordWasteMovement($variant, $inspection['quantity'], $returnOrder);
                }
            }
        }
    }

    private function recordWasteMovement(ProductVariant $variant, int $quantity, ReturnOrder $returnOrder): void
    {
        $variant = ProductVariant::lockForUpdate()->find($variant->id);
        if (! $variant) {
            return;
        }

        $variant->stockMovements()->create([
            'type' => StockMovementType::WASTE,
            'quantity' => $quantity,
            'quantity_before' => $variant->quantity,
            'quantity_after' => $variant->quantity,
            'sourceable_type' => $returnOrder->getMorphClass(),
            'sourceable_id' => $returnOrder->id,
            'description' => "إتلاف المرتجع #{$returnOrder->return_number} (كمية {$quantity})",
        ]);
    }

    private function recordTransaction(ReturnOrder $returnOrder, Money $difference, string $reference, User $adminUser): void
    {
        $transactionType = $difference->isPositive() ? TransactionType::Payment : TransactionType::Refund;
        $amount = $difference->abs();

        $this->transactionService->record(new TransactionEntryData(
            order_id: $returnOrder->order_id,
            user_id: $adminUser->getKey(),
            type: $transactionType,
            payment_method: $returnOrder->order->payment_method,
            amount: (float) $amount->getAmount()->__toString(),
            currency: 'USD',
            status: TransactionStatus::Success,
            transaction_ref: $reference,
            gateway_response: [],
            description: "تسوية مالية للمرتجع #{$returnOrder->return_number}"
        ));
    }

    private function updateOrderStatus(ReturnOrder $returnOrder, Money $refundTotal): void
    {
        $order = $returnOrder->order;
        $paymentStatus = $order->payment_status;

        if ($refundTotal->isGreaterThan(Money::zero('USD'))) {
            $grandTotal = Money::of((string) $order->grand_total, 'USD');
            $paymentStatus = $refundTotal->isGreaterThanOrEqualTo($grandTotal)
                ? PaymentStatus::REFUNDED
                : PaymentStatus::PARTIALLY_REFUNDED;
        }

        $order->update([
            'status' => OrderStatus::RETURNED,
            'payment_status' => $paymentStatus,
        ]);
    }
}
