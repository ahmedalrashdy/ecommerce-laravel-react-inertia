<?php

namespace App\Services\Orders;

use App\Data\Orders\CancelOrderData;
use App\Data\Orders\CancelOrderResult;
use App\Enums\CancelRefundOption;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\StockMovementType;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Services\Inventory\InventoryService;
use App\Services\Payments\RefundService;
use Exception;
use Illuminate\Support\Facades\DB;

class OrderCancellationService
{
    public function __construct(
        protected InventoryService $inventoryService,
        protected RefundService $refundService
    ) {}

    public function cancel(Order $order, CancelOrderData $data): CancelOrderResult
    {
        // 1. التحقق من إمكانية الإلغاء
        if (in_array($order->status, [OrderStatus::SHIPPED, OrderStatus::DELIVERED], true)) {
            throw new Exception(__('filament.orders.cannot_cancel_after_shipping'));
        }

        if ($order->payment_status !== PaymentStatus::PAID) {
            throw new Exception(__('filament.orders.cannot_cancel_unpaid'));
        }

        if ($order->status === OrderStatus::CANCELLED) {
            throw new Exception(__('filament.orders.order_already_cancelled'));
        }

        return DB::transaction(function () use ($order, $data): CancelOrderResult {

            // 2. تحديث الحالة
            $order->update([
                'status' => OrderStatus::CANCELLED,
                'cancellation_reason' => $data->reason,
                'cancelled_at' => now(),
            ]);

            OrderHistory::create([
                'order_id' => $order->id,
                'status' => OrderStatus::CANCELLED,
                'comment' => __('filament.orders.cancelled_with_reason', [
                    'reason' => $data->reason,
                ]),
                'is_visible_to_user' => true,
                'actor_type' => $data->cancelledBy?->getMorphClass(),
                'actor_id' => $data->cancelledBy?->getKey(),
            ]);

            // 3. إعادة المخزون (Restock)
            // نحتاج للمرور على كل عنصر وإعادته
            $order->loadMissing('items.productVariant');

            $hasSaleMovement = $order->stockMovements()
                ->where('type', StockMovementType::SALE)
                ->exists();

            $hasCancellationMovement = $order->stockMovements()
                ->where('type', StockMovementType::ORDER_CANCELLATION)
                ->exists();

            if ($hasSaleMovement && ! $hasCancellationMovement) {
                foreach ($order->items as $item) {
                    // ملاحظة: تأكد من تحميل productVariant مسبقاً لتجنب N+1
                    if ($item->productVariant) {
                        $this->inventoryService->increaseStock(
                            $item->productVariant,
                            $item->quantity,
                            StockMovementType::ORDER_CANCELLATION,
                            $order, // المصدر
                            "إلغاء الطلب #{$order->order_number}"
                        );
                    }
                }
            }

            // 4. التعامل المالي (النقطة الجوهرية)
            return $this->handleFinancials($order, $data);
        });
    }

    protected function handleFinancials(Order $order, CancelOrderData $data): CancelOrderResult
    {
        $autoRefundAttempted = false;
        $autoRefundSucceeded = false;
        $refundOption = $data->refundOption ?? CancelRefundOption::AUTO;
        $refundRequired = false;

        if (in_array($order->payment_status, [PaymentStatus::PAID, PaymentStatus::REFUND_PENDING, PaymentStatus::PARTIALLY_REFUNDED], true)) {
            $refundRequired = true;
            $order->update(['payment_status' => PaymentStatus::REFUND_PENDING]);

            if ($refundOption === CancelRefundOption::AUTO) {
                $autoRefundAttempted = true;
                $autoRefundSucceeded = $this->refundService->processAutoRefund($order);
            }
        } elseif ($order->payment_status === PaymentStatus::PENDING) {
            $order->update(['payment_status' => PaymentStatus::FAILED]);
        }

        return new CancelOrderResult(
            autoRefundAttempted: $autoRefundAttempted,
            autoRefundSucceeded: $autoRefundSucceeded,
            refundRequired: $refundRequired,
        );
    }
}
