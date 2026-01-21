<?php

namespace App\Http\Controllers\Store\Payments;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Payments\PaymentCheckoutService;
use App\Traits\FlashMessage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;

class OrderPaymentController extends Controller
{
    use FlashMessage;

    public function start(
        Request $request,
        Order $order,
        PaymentCheckoutService $checkoutService
    ) {
        $user = $request->user();

        if ($order->user_id !== $user->id) {
            abort(403);
        }

        try {
            $session = $checkoutService->startCheckout($order);

            return Inertia::location($session->url);
        } catch (Throwable $exception) {
            $this->flashError($exception->getMessage());

            return redirect()
                ->route('store.payments.failed', $order);
        }
    }
}
