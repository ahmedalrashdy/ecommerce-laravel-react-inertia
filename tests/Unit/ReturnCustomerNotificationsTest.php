<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\ReturnOrder;
use App\Models\User;
use App\Notifications\Returns\CustomerReturnApprovedNotification;
use App\Notifications\Returns\CustomerReturnInspectedNotification;
use App\Notifications\Returns\CustomerReturnReceivedNotification;
use App\Notifications\Returns\CustomerReturnShippedBackNotification;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ReturnCustomerNotificationsTest extends TestCase
{
    public static function notificationProvider(): array
    {
        return [
            [CustomerReturnApprovedNotification::class],
            [CustomerReturnShippedBackNotification::class],
            [CustomerReturnReceivedNotification::class],
            [CustomerReturnInspectedNotification::class],
        ];
    }

    #[DataProvider('notificationProvider')]
    public function test_notifications_use_orders_high_priority_queue(string $notificationClass): void
    {
        config()->set('queue.queues.orders_notifications', 'orders-notifications-high');

        $notification = new $notificationClass($this->makeReturnOrder());

        $this->assertSame([
            'database' => 'orders-notifications-high',
            'mail' => 'orders-notifications-high',
        ], $notification->viaQueues());
    }

    #[DataProvider('notificationProvider')]
    public function test_notification_payload_contains_return_and_order_numbers(string $notificationClass): void
    {
        $user = User::make([
            'name' => 'Test User',
        ]);

        $notification = new $notificationClass($this->makeReturnOrder());

        $payload = $notification->toArray($user);

        $this->assertSame('RMA-00010', $payload['return_number']);
        $this->assertSame('ORD-00010', $payload['order_number']);
    }

    private function makeReturnOrder(): ReturnOrder
    {
        $order = Order::make([
            'order_number' => 'ORD-00010',
        ]);

        $returnOrder = ReturnOrder::make([
            'return_number' => 'RMA-00010',
        ]);

        $returnOrder->setRelation('order', $order);

        return $returnOrder;
    }
}
