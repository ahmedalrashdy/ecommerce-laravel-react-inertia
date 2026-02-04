<?php

namespace App\Services\Orders;

use App\Data\Orders\OrderStatusTransitionData;
use App\Enums\OrderStatus;
use App\Events\Orders\OrderDelivered;
use App\Events\Orders\OrderShipped;
use App\Models\Order;
use App\Models\OrderHistory;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OrderStatusService
{
    public function transition(Order $order, OrderStatusTransitionData $data): Order
    {
        if (! $this->canTransition($order->status, $data->nextStatus)) {
            throw new RuntimeException(__('validation.invalid_status_transition'));
        }

        return DB::transaction(function () use ($order, $data): Order {
            $order->update(array_merge($data->updateAttributes(), [
                'status' => $data->nextStatus,
            ]));

            OrderHistory::create([
                'order_id' => $order->id,
                'status' => $data->nextStatus,
                'comment' => $data->comment,
                'is_visible_to_user' => $data->visibleToUser,
                'actor_type' => $data->actor?->getMorphClass(),
                'actor_id' => $data->actor?->getKey(),
            ]);

            if ($data->nextStatus === OrderStatus::SHIPPED && $data->notifyCustomer) {
                event(new OrderShipped($order));
            }

            if ($data->nextStatus === OrderStatus::DELIVERED && $data->notifyCustomerOnDelivery) {
                event(new OrderDelivered($order));
            }

            return $order;
        });
    }

    private function canTransition(OrderStatus $current, OrderStatus $next): bool
    {
        $map = [
            OrderStatus::PENDING->value => [
                OrderStatus::PROCESSING,
                OrderStatus::CANCELLED,
            ],
            OrderStatus::PROCESSING->value => [
                OrderStatus::SHIPPED,
                OrderStatus::CANCELLED,
            ],
            OrderStatus::SHIPPED->value => [
                OrderStatus::DELIVERED,
            ],
            OrderStatus::DELIVERED->value => [],
            OrderStatus::CANCELLED->value => [],
            OrderStatus::RETURNED->value => [],
        ];

        return in_array($next, $map[$current->value] ?? [], true);
    }
}
