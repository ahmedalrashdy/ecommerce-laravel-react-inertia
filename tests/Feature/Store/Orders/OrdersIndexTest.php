<?php

namespace Tests\Feature\Store\Orders;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\ReturnOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrdersIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_orders_index_shows_active_tab_by_default(): void
    {
        $user = User::factory()->create();
        $activeOrder = Order::factory()->for($user)->create([
            'status' => OrderStatus::PENDING,
        ]);
        $historyOrder = Order::factory()->for($user)->create([
            'status' => OrderStatus::DELIVERED,
        ]);
        ReturnOrder::factory()
            ->for($historyOrder, 'order')
            ->for($user)
            ->create();
        Order::factory()->create([
            'status' => OrderStatus::PENDING,
        ]);

        $response = $this->actingAs($user)->get(route('store.orders.index'));

        $response->assertInertia(
            fn ($page) => $page
                ->component('store/orders/index')
                ->where('tab', 'active')
                ->has('orders', 1)
                ->where('orders.0.orderNumber', $activeOrder->order_number)
                ->has('returns', 0)
                ->where('counts.active', 1)
                ->where('counts.history', 1)
                ->where('counts.returns', 1)
        );
    }

    public function test_orders_index_filters_history_tab(): void
    {
        $user = User::factory()->create();
        Order::factory()->for($user)->create([
            'status' => OrderStatus::PROCESSING,
        ]);
        $historyOrder = Order::factory()->for($user)->create([
            'status' => OrderStatus::CANCELLED,
        ]);

        $response = $this->actingAs($user)->get(route('store.orders.index', [
            'tab' => 'history',
        ]));

        $response->assertInertia(
            fn ($page) => $page
                ->component('store/orders/index')
                ->where('tab', 'history')
                ->has('orders', 1)
                ->where('orders.0.orderNumber', $historyOrder->order_number)
                ->where('counts.active', 1)
                ->where('counts.history', 1)
                ->where('counts.returns', 0)
        );
    }

    public function test_orders_index_filters_returns_tab(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create([
            'status' => OrderStatus::DELIVERED,
        ]);
        $returnOrder = ReturnOrder::factory()
            ->for($order, 'order')
            ->for($user)
            ->create();

        $response = $this->actingAs($user)->get(route('store.orders.index', [
            'tab' => 'returns',
        ]));

        $response->assertInertia(
            fn ($page) => $page
                ->component('store/orders/index')
                ->where('tab', 'returns')
                ->has('returns', 1)
                ->where('returns.0.returnNumber', $returnOrder->return_number)
                ->has('orders', 0)
                ->where('counts.active', 0)
                ->where('counts.history', 1)
                ->where('counts.returns', 1)
        );
    }
}
