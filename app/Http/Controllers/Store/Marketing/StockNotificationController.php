<?php

namespace App\Http\Controllers\Store\Marketing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store\StockNotification\SubscribeRequest;
use App\Models\ProductVariant;
use App\Services\StockNotification\StockNotificationService;
use App\Traits\FlashMessage;

class StockNotificationController extends Controller
{
    use FlashMessage;

    public function __construct(
        private StockNotificationService $stockNotificationService
    ) {}

    /**
     * Subscribe to stock notification for a product variant
     */
    public function store(SubscribeRequest $request)
    {
        $user = auth()->user();
        $variant = ProductVariant::findOrFail($request->validated()['variant_id']);
        $this->stockNotificationService->subscribe($user, $variant);
        $this->flashSuccess('تم الإشتراك بنجاح سيتم إشعارك عند توفر المنتج');

        return back();
    }
}
