<?php

namespace App\Services\Returns;

use App\Data\Returns\AdminReturnCreationData;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\ReturnStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ReturnOrder;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminReturnCreationService
{
    public function create(AdminReturnCreationData $data, User $admin): ReturnOrder
    {
        $order = Order::findOrFail($data->getOrderId());

        if ($order->status !== OrderStatus::DELIVERED) {
            throw new Exception('لا يمكن استرجاع طلب لم يتم توصيله بعد.');
        }

        if ($order->type === OrderType::RETURN_SHIPMENT) {
            throw new Exception(__('validation.return_order_reshipment_not_allowed'));
        }

        if (! in_array($data->status, [ReturnStatus::REQUESTED, ReturnStatus::APPROVED], true)) {
            throw new Exception('حالة المرتجع غير مدعومة.');
        }

        return DB::transaction(function () use ($data, $order, $admin): ReturnOrder {
            $defaultReason = $data->reason;

            if (! $defaultReason) {
                $defaultReason = $data->items->first()['reason'] ?? null;
            }

            $returnOrder = ReturnOrder::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'return_number' => 'RMA-'.strtoupper(Str::random(8)),
                'status' => $data->status,
                'reason' => $defaultReason,
            ]);

            foreach ($data->items as $itemData) {
                $orderItem = OrderItem::where('order_id', $order->id)
                    ->where('id', $itemData['order_item_id'])
                    ->firstOrFail();

                if ($itemData['quantity'] > $orderItem->quantity) {
                    throw new Exception("الكمية المسترجعة ({$itemData['quantity']}) أكبر من المشتراة.");
                }

                $returnOrder->items()->create([
                    'order_item_id' => $orderItem->id,
                    'quantity' => $itemData['quantity'],
                    'reason' => $itemData['reason'] ?? $defaultReason,
                ]);
            }

            $returnOrder->history()->create([
                'status' => $data->status,
                'comment' => $data->status === ReturnStatus::APPROVED
                    ? __('filament.returns.manual_return_approved')
                    : __('filament.returns.manual_return_created'),
                'actor_type' => $admin->getMorphClass(),
                'actor_id' => $admin->getKey(),
            ]);

            return $returnOrder;
        });
    }
}
