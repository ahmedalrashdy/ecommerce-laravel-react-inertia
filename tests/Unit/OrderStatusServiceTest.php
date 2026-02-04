<?php

namespace Tests\Unit;

use App\Data\Orders\OrderStatusTransitionData;
use App\Enums\OrderStatus;
use App\Events\Orders\OrderDelivered;
use App\Events\Orders\OrderShipped;
use App\Models\Order;
use App\Services\Orders\OrderStatusService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class OrderStatusServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_shipped_event_is_dispatched_when_notify_customer_is_true(): void
    {
        Event::fake([OrderShipped::class]);

        $order = Order::factory()->create([
            'status' => OrderStatus::PROCESSING,
        ]);

        app(OrderStatusService::class)->transition($order, new OrderStatusTransitionData(
            nextStatus: OrderStatus::SHIPPED,
            notifyCustomer: true,
        ));

        Event::assertDispatched(OrderShipped::class, function (OrderShipped $event) use ($order): bool {
            return $event->order->is($order);
        });
    }

    public function test_shipped_event_is_not_dispatched_when_notify_customer_is_false(): void
    {
        Event::fake([OrderShipped::class]);

        $order = Order::factory()->create([
            'status' => OrderStatus::PROCESSING,
        ]);

        app(OrderStatusService::class)->transition($order, new OrderStatusTransitionData(
            nextStatus: OrderStatus::SHIPPED,
            notifyCustomer: false,
        ));

        Event::assertNotDispatched(OrderShipped::class);
    }

    public function test_delivered_event_is_dispatched_when_notify_customer_is_true(): void
    {
        Event::fake([OrderDelivered::class]);

        $order = Order::factory()->create([
            'status' => OrderStatus::SHIPPED,
        ]);

        app(OrderStatusService::class)->transition($order, new OrderStatusTransitionData(
            nextStatus: OrderStatus::DELIVERED,
            notifyCustomerOnDelivery: true,
        ));

        Event::assertDispatched(OrderDelivered::class, function (OrderDelivered $event) use ($order): bool {
            return $event->order->is($order);
        });
    }

    public function test_delivered_event_is_not_dispatched_when_notify_customer_is_false(): void
    {
        Event::fake([OrderDelivered::class]);

        $order = Order::factory()->create([
            'status' => OrderStatus::SHIPPED,
        ]);

        app(OrderStatusService::class)->transition($order, new OrderStatusTransitionData(
            nextStatus: OrderStatus::DELIVERED,
            notifyCustomerOnDelivery: false,
        ));

        Event::assertNotDispatched(OrderDelivered::class);
    }
}
