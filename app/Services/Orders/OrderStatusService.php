<?php

namespace App\Services\Orders;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderHistory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OrderStatusService
{
    public function transition(
        Order $order,
        OrderStatus $nextStatus,
        ?string $comment = null,
        ?Model $actor = null,
        bool $visibleToUser = true,
        array $attributes = [],
    ): Order {
        if (! $this->canTransition($order->status, $nextStatus)) {
            throw new RuntimeException(__('validation.invalid_status_transition'));
        }

        return DB::transaction(function () use ($order, $nextStatus, $comment, $actor, $visibleToUser, $attributes): Order {
            $order->update(array_merge($attributes, [
                'status' => $nextStatus,
            ]));

            OrderHistory::create([
                'order_id' => $order->id,
                'status' => $nextStatus,
                'comment' => $comment,
                'is_visible_to_user' => $visibleToUser,
                'actor_type' => $actor?->getMorphClass(),
                'actor_id' => $actor?->getKey(),
            ]);

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
