<?php

namespace App\Http\Controllers\Store\Account\Orders;

use App\Data\Returns\ReturnCreationData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Return\StoreReturnRequest;
use App\Models\Order;
use App\Services\Returns\ReturnRequestService;
use App\Traits\FlashMessage;
use Illuminate\Http\RedirectResponse;
use Throwable;

class OrderReturnController extends Controller
{
    use FlashMessage;

    public function __invoke(
        StoreReturnRequest $request,
        Order $order,
        ReturnRequestService $returnRequestService
    ): RedirectResponse {
        $items = collect($request->input('items', []));
        $returnType = $request->string('return_type')->toString();

        if ($returnType === 'full') {
            $order->loadMissing('items');

            $items = $order->items->map(function ($item) use ($request) {
                return [
                    'order_item_id' => $item->id,
                    'quantity' => $item->quantity,
                    'reason' => $request->string('reason')->toString(),
                ];
            });
        }

        $payload = ReturnCreationData::from([
            'orderId' => $order->id,
            'reason' => $request->string('reason')->toString() ?: null,
            'items' => $items,
        ]);

        try {
            $returnRequestService->createRequest($payload, $request->user());
        } catch (Throwable $exception) {
            return redirect()
                ->back()
                ->withErrors([
                    'return' => $exception->getMessage(),
                ]);
        }

        $this->flashSuccess('تم تسجيل طلب الاسترجاع بنجاح.');

        return redirect()->route('store.account.orders.show', $order);
    }
}
