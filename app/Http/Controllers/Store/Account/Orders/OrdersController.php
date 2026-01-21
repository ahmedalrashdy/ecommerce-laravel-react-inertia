<?php

namespace App\Http\Controllers\Store\Account\Orders;

use App\Data\Basic\OrderDetailsData;
use App\Data\Basic\OrderListItemData;
use App\Data\Basic\OrderSummaryData;
use App\Data\Basic\ReturnSummaryData;
use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ReturnOrder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrdersController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $tab = $this->resolveTab($request->query('tab'));

        $activeStatuses = [
            OrderStatus::PENDING,
            OrderStatus::PROCESSING,
            OrderStatus::SHIPPED,
        ];
        $historyStatuses = [
            OrderStatus::DELIVERED,
            OrderStatus::CANCELLED,
        ];

        $activeCount = $user->orders()
            ->whereIn('status', $activeStatuses)
            ->count();
        $historyCount = $user->orders()
            ->whereIn('status', $historyStatuses)
            ->count();
        $returnsCount = ReturnOrder::query()
            ->where('user_id', $user->id)
            ->count();

        $orders = [];
        $returns = [];

        if ($tab !== 'returns') {
            $targetStatuses = $tab === 'history' ? $historyStatuses : $activeStatuses;

            $orders = $user->orders()
                ->with(['items.product.category', 'items.product.brand'])
                ->withCount('items')
                ->whereIn('status', $targetStatuses)
                ->latest()
                ->get();

            $orders = OrderListItemData::collect($orders);
        }

        if ($tab === 'returns') {
            $returns = ReturnOrder::query()
                ->with(['order'])
                ->withCount('items')
                ->where('user_id', $user->id)
                ->latest()
                ->get();

            $returns = ReturnSummaryData::collect($returns);
        }

        return Inertia::render('store/account/orders/index', [
            'tab' => $tab,
            'counts' => [
                'active' => $activeCount,
                'history' => $historyCount,
                'returns' => $returnsCount,
            ],
            'orders' => $orders,
            'returns' => $returns,
        ]);
    }

    public function show(Request $request, Order $order): Response
    {
        $user = $request->user();

        if ($order->user_id !== $user->id) {
            abort(404);
        }

        return Inertia::render('store/account/orders/show', [
            'order' => OrderDetailsData::fromModel($order, $user),
            'summary' => OrderSummaryData::fromModel($order),
        ]);
    }

    protected function resolveTab(?string $tab): string
    {
        if (! in_array($tab, ['active', 'history', 'returns'], true)) {
            return 'active';
        }

        return $tab;
    }
}
