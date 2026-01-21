<?php

namespace App\Http\Controllers\Store\Payments;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PaymentStatusController extends Controller
{
    public function success(Request $request, Order $order): Response
    {
        $user = $request->user();

        if ($order->user_id !== $user->id) {
            abort(404);
        }

        return Inertia::render('store/payments/success', [
            'order' => [
                'id' => $order->id,
                'orderNumber' => $order->order_number,
                'paymentStatusLabel' => $order->payment_status->getLabel(),
                'statusLabel' => $order->status->getLabel(),
            ],
        ]);
    }

    public function failed(Request $request, Order $order): Response
    {
        $user = $request->user();

        if ($order->user_id !== $user->id) {
            abort(404);
        }

        return Inertia::render('store/payments/failed', [
            'order' => [
                'id' => $order->id,
                'orderNumber' => $order->order_number,
                'paymentStatusLabel' => $order->payment_status->getLabel(),
                'statusLabel' => $order->status->getLabel(),
            ],
        ]);
    }
}
