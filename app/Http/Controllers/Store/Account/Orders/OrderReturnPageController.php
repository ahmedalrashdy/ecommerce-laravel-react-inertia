<?php

namespace App\Http\Controllers\Store\Account\Orders;

use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderReturnPageController extends Controller
{
    public function show(Request $request, Order $order): Response|RedirectResponse
    {

        $user = $request->user();

        if ($order->user_id !== $user->id) {
            abort(404);
        }

        if ($order->type === OrderType::RETURN_SHIPMENT) {
            return redirect()
                ->route('store.account.orders.show', $order)
                ->withErrors([
                    'return' => 'لا يمكن إنشاء مرتجع لطلبات إعادة الشحن.',
                ]);
        }

        $deliveredAt = $order->history()
            ->where('status', OrderStatus::DELIVERED)
            ->latest('created_at')
            ->first()
            ?->created_at;

        $returnWindowEndsAt = $deliveredAt
            ? $deliveredAt->copy()->addDays(14)
            : null;
        $canReturn = $order->status === OrderStatus::DELIVERED
            && $returnWindowEndsAt
            && now()->lessThanOrEqualTo($returnWindowEndsAt);
        if (! $canReturn) {
            return redirect()
                ->route('store.account.orders.show', $order)
                ->withErrors([
                    'return' => 'لا يمكن إنشاء طلب استرجاع لهذا الطلب حالياً.',
                ]);
        }

        $order->load('items');

        $items = $order->items->map(function (OrderItem $item) {
            return [
                'id' => $item->id,
                'productId' => $item->product_id,
                'name' => $item->product_name,
                'image' => $item->product_variant_snapshot['variant']['default_image'] ?? null,
                'attributes' => $item->attributes_list,
                'quantity' => $item->quantity,
            ];
        })->values()->toArray();

        return Inertia::render('store/account/orders/return', [
            'order' => [
                'id' => $order->id,
                'orderNumber' => $order->order_number,
                'returnWindowEndsAt' => $returnWindowEndsAt?->translatedFormat('d F Y'),
            ],
            'items' => $items,
        ]);
    }
}
