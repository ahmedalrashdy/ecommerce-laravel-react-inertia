<?php

namespace App\Http\Controllers\Store\Account\Orders;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreReviewRequest;
use App\Models\Order;
use App\Models\Review;
use App\Traits\FlashMessage;
use Illuminate\Http\RedirectResponse;

class OrderReviewController extends Controller
{
    use FlashMessage;

    public function __invoke(StoreReviewRequest $request, Order $order): RedirectResponse
    {
        Review::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'product_id' => $request->integer('product_id'),
            ],
            [
                'rating' => $request->integer('rating'),
                'comment' => $request->string('comment')->toString(),
                'is_approved' => false,
            ]
        );

        $this->flashSuccess('تم حفظ تقييمك بنجاح.');

        return redirect()->route('store.account.orders.show', $order);
    }
}
