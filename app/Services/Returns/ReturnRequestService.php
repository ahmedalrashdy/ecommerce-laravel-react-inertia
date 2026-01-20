<?php

namespace App\Services\Returns;

use App\Data\Returns\ReturnCreationData;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\ReturnStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ReturnOrder;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReturnRequestService
{
    public function createRequest(ReturnCreationData $data, $user): ReturnOrder
    {
        $order = Order::findOrFail($data->orderId);

        // 1. التحقق من الأهلية
        // يجب أن يكون الطلب قد تم توصيله (Delivered)
        if ($order->status !== OrderStatus::DELIVERED) {
            throw new Exception('لا يمكن استرجاع طلب لم يتم توصيله بعد.');
        }

        if ($order->type === OrderType::RETURN_SHIPMENT) {
            throw new Exception('لا يمكن إنشاء مرتجع لطلبات إعادة الشحن.');
        }

        $deliveredAt = $order->history()
            ->where('status', OrderStatus::DELIVERED)
            ->latest('created_at')
            ->first()
            ?->created_at;

        if (! $deliveredAt) {
            throw new Exception('لا يمكن استرجاع طلب بدون تاريخ تسليم واضح.');
        }

        $returnDeadline = Carbon::parse($deliveredAt)->addDays(14);

        if (now()->greaterThan($returnDeadline)) {
            throw new Exception('انتهت مهلة الاسترجاع لهذا الطلب.');
        }

        return DB::transaction(function () use ($data, $order, $user) {
            $defaultReason = $data->reason;

            if (! $defaultReason) {
                $defaultReason = $data->items->first()['reason'] ?? null;
            }

            // 2. إنشاء رأس المرتجع
            $returnOrder = ReturnOrder::create([
                'order_id' => $order->id,
                'user_id' => $user->id,
                'return_number' => 'RMA-'.strtoupper(Str::random(8)),
                'status' => ReturnStatus::REQUESTED,
                'reason' => $defaultReason,
            ]);

            // 3. إضافة العناصر
            foreach ($data->items as $itemData) {
                // التأكد من أن العنصر يتبع لنفس الطلب
                $orderItem = OrderItem::where('order_id', $order->id)
                    ->where('id', $itemData['order_item_id'])
                    ->firstOrFail();

                // التحقق من الكمية (لا يرجع أكثر مما اشترى)
                // تحسين مستقبلي: التحقق من المرتجعات السابقة لنفس العنصر
                if ($itemData['quantity'] > $orderItem->quantity) {
                    throw new Exception("الكمية المسترجعة ({$itemData['quantity']}) أكبر من المشتراة.");
                }

                $returnOrder->items()->create([
                    'order_item_id' => $orderItem->id,
                    'quantity' => $itemData['quantity'],
                    'reason' => $itemData['reason'] ?? $defaultReason,
                ]);
            }

            // 4. تسجيل التاريخ
            $returnOrder->history()->create([
                'status' => ReturnStatus::REQUESTED,
                'comment' => 'تم فتح طلب الاسترجاع من قبل العميل',
                'actor_type' => get_class($user),
                'actor_id' => $user->id,
            ]);

            return $returnOrder;
        });
    }
}
