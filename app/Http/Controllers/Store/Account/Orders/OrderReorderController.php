<?php

namespace App\Http\Controllers\Store\Account\Orders;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Orders\OrderReorderService;
use App\Traits\FlashMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrderReorderController extends Controller
{
    use FlashMessage;

    public function __invoke(
        Request $request,
        Order $order,
        OrderReorderService $orderReorderService
    ): RedirectResponse {
        $user = $request->user();

        if ($order->user_id !== $user->id) {
            abort(404);
        }

        $result = $orderReorderService->reorder(
            $order,
            $user,
            $request->session()->getId()
        );

        if ($result['added'] === 0) {
            $this->flashToast('warning', 'لا توجد عناصر متاحة لإعادة الطلب حالياً.');

            return redirect()->route('store.cart.index');
        }

        $message = 'تمت إضافة العناصر إلى السلة بنجاح.';

        if ($result['skipped'] > 0) {
            $message = "تمت إضافة {$result['added']} عناصر، وتعذر إضافة {$result['skipped']} عناصر بسبب نفاد المخزون.";
        }

        $this->flashSuccess($message);

        return redirect()->route('store.cart.index');
    }
}
