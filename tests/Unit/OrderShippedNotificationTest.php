<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\User;
use App\Notifications\Orders\OrderShippedNotification;
use Tests\TestCase;

class OrderShippedNotificationTest extends TestCase
{
    public function test_notification_uses_orders_high_priority_queue_for_all_channels(): void
    {
        config()->set('queue.queues.orders_notifications', 'orders-notifications-high');

        $order = Order::make([
            'order_number' => 'ORD-00010',
        ]);
        $order->setAttribute('id', 10);

        $notification = new OrderShippedNotification($order);

        $this->assertSame([
            'database' => 'orders-notifications-high',
            'mail' => 'orders-notifications-high',
        ], $notification->viaQueues());
    }

    public function test_notification_array_payload_contains_order_details(): void
    {
        $user = User::make([
            'name' => 'Test User',
        ]);
        $order = Order::make([
            'order_number' => 'ORD-00010',
        ]);
        $order->setAttribute('id', 10);

        $notification = new OrderShippedNotification($order);

        $payload = $notification->toArray($user);

        $this->assertSame('ORD-00010', $payload['order_number']);
    }
}
