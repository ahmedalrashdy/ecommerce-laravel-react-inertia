<?php

namespace App\Services\Returns;

use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\PaymentStatus;
use App\Enums\ReturnResolution;
use App\Enums\ReturnStatus;
use App\Enums\StockMovementType;
use App\Models\Order;
use App\Models\ReturnItemInspection;
use App\Models\ReturnOrder;
use App\Services\Inventory\InventoryService;
use App\Services\Payments\RefundService;
use Exception;
use Illuminate\Support\Facades\DB;

class ReturnResolutionService
{
    public function __construct(
        protected RefundService $refundService,
        protected InventoryService $inventoryService
    ) {}

    /**
     * تنفيذ القرارات (المالية والتشغيلية) بعد الفحص
     */
    public function resolve(ReturnOrder $returnOrder, $adminUser)
    {
        if ($returnOrder->status !== ReturnStatus::INSPECTED) {
            throw new Exception('يجب فحص المرتجع أولاً.');
        }
        $returnOrder->load('items.inspections', 'order');
        DB::transaction(function () use ($returnOrder, $adminUser) {

            $totalRefundAmount = 0;
            $itemsToReplace = [];

            // 1. تحليل القرارات لكل عنصر
            foreach ($returnOrder->items as $returnItem) {

                /** @var ReturnItemInspection $inspection */
                foreach ($returnItem->inspections as $inspection) {
                    if ($inspection->resolution === ReturnResolution::REFUND) {
                        $unitPrice = (float) $returnItem->orderItem->price;
                        $refundAmount = $inspection->refund_amount !== null
                            ? (float) $inspection->refund_amount
                            : $unitPrice * $inspection->quantity;

                        $totalRefundAmount += $refundAmount;
                    }

                    if ($inspection->resolution === ReturnResolution::REPLACEMENT) {
                        $itemsToReplace[] = [
                            'variant' => $returnItem->orderItem->productVariant,
                            'quantity' => $inspection->quantity,
                        ];
                    }
                }
            }

            // 2. تنفيذ إرجاع المال (إذا وجد مبلغ)
            if ($totalRefundAmount > 0) {
                $finalRefund = $totalRefundAmount;

                $returnOrder->update(['refund_amount' => $finalRefund]);

                // محاولة الإرجاع (يدوي حالياً لضمان الدقة في المرتجعات)
                $this->refundService->processManualRefund(
                    $returnOrder->order,
                    "RMA Refund #{$returnOrder->return_number}",
                    $finalRefund
                );
            }

            // 3. تنفيذ الاستبدال (إنشاء طلب جديد)
            if (! empty($itemsToReplace)) {
                $this->createReplacementOrder($returnOrder, $itemsToReplace);
            }

            // 4. إغلاق المرتجع
            $returnOrder->update(['status' => ReturnStatus::COMPLETED]);

            // 5. تسجيل التاريخ
            $returnOrder->history()->create([
                'status' => ReturnStatus::COMPLETED,
                'comment' => 'تم تنفيذ القرار وإغلاق الملف',
                'actor_type' => get_class($adminUser),
                'actor_id' => $adminUser->id,
            ]);

            $newPaymentStatus = $returnOrder->order->payment_status; // نحتفظ بالحالة القديمة (Paid) افتراضياً

            if ($totalRefundAmount > 0) {
                // فقط إذا كان هناك مال خرج من الخزنة نغير الحالة
                $newPaymentStatus = ($totalRefundAmount >= $returnOrder->order->grand_total)
                    ? PaymentStatus::REFUNDED
                    : PaymentStatus::PARTIALLY_REFUNDED;
            }

            // تحديث الطلب الأصلي
            $returnOrder->order->update([
                'status' => OrderStatus::RETURNED,
                'payment_status' => $newPaymentStatus,
            ]);
        });
    }

    /**
     * إنشاء طلب استبدال مجاني
     */
    protected function createReplacementOrder(ReturnOrder $returnOrder, array $items)
    {
        $originalOrder = $returnOrder->order;

        // إنشاء رأس الطلب
        $replacementOrder = Order::create([
            'user_id' => $originalOrder->user_id,
            'parent_order_id' => $originalOrder->id, // الربط بالأب
            'type' => OrderType::REPLACEMENT, // النوع الجديد
            'order_number' => 'REP-'.$originalOrder->order_number,
            'status' => OrderStatus::PENDING,
            'payment_status' => PaymentStatus::PAID, // مدفوع مسبقاً (مجاني)
            'payment_method' => $originalOrder->payment_method,

            // نسخ العناوين
            'shipping_address_snapshot' => $originalOrder->shipping_address_snapshot,

            // المبالغ (أصفار لأنه استبدال)
            'subtotal' => 0,
            'grand_total' => 0,
            'notes' => "استبدال للمرتجع رقم {$returnOrder->return_number}",
        ]);

        // إضافة العناصر وخصم المخزون
        foreach ($items as $data) {
            $variant = $data['variant'];
            $qty = $data['quantity'];

            // 1. عنصر الطلب
            $replacementOrder->items()->create([
                'product_variant_id' => $variant->id,
                'product_id' => $variant->product_id,
                'product_variant_snapshot' => [
                    'name' => $variant->product->name,
                    'sku' => $variant->sku,
                ],
                'price' => 0, // مجاني
                'quantity' => $qty,
            ]);

            // 2. خصم المخزون (نوع الحركة: REPLACEMENT_OUT)
            $this->inventoryService->decreaseStock(
                $variant,
                $qty,
                StockMovementType::REPLACEMENT_OUT, // 7 (النوع الجديد)
                $replacementOrder,
                "صرف استبدال للطلب #{$replacementOrder->order_number}"
            );
        }

        // يمكن إطلاق Event(OrderCreated) هنا ليرسل إيميل للعميل بالطلب الجديد
    }
}
