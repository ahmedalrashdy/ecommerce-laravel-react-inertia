<?php

namespace App\Http\Controllers\Store\Checkout;

use App\Data\Orders\AddressData;
use App\Data\Orders\CheckoutData;
use App\Data\Orders\OrderItemData;
use App\Enums\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Models\Order;
use App\Models\UserAddress;
use App\Services\Cart\CartService;
use App\Services\Checkout\OrderCheckoutService;
use App\Traits\FlashMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class PlaceOrderController extends Controller
{
    use FlashMessage;

    /**
     * Handle the incoming request.
     */
    public function __invoke(
        StoreOrderRequest $request,
        CartService $cartService,
        OrderCheckoutService $checkoutService
    ): RedirectResponse {
        $user = $request->user();
        $idempotencyKey = (string) $request->header('X-Idempotency-Key');
        $validated = $request->validated();
        $selectedVariantIds = array_map('intval', $validated['selected_items']);

        $existingOrder = Order::query()
            ->where('user_id', $user->id)
            ->where('idempotency_key', $idempotencyKey)
            ->first();

        if ($existingOrder) {
            return redirect()->route('store.payments.start', $existingOrder);
        }

        $cart = $cartService->getOrCreateCart($user, $request->session()->getId());
        $cartItems = $cart->items()
            ->with(['productVariant.product', 'productVariant.attributeValues.attribute', 'productVariant.defaultImage'])
            ->where('is_selected', true)
            ->whereIn('product_variant_id', $selectedVariantIds)
            ->get();

        if ($cartItems->count() !== count($selectedVariantIds)) {
            throw ValidationException::withMessages([
                'selected_items' => 'المنتجات المحددة غير متوفرة في السلة.',
            ]);
        }

        $shippingAddress = UserAddress::where('user_id', $user->id)
            ->findOrFail($validated['shipping_address_id']);

        $checkoutData = new CheckoutData(
            user: $user,
            shippingAddress: AddressData::fromModel($shippingAddress),
            paymentMethod: PaymentMethod::PENDING,
            items: $cartItems->map(fn ($item) => OrderItemData::fromCartItem($item)),
            idempotencyKey: $idempotencyKey,
            notes: $validated['notes'] ?? null
        );

        $order = $checkoutService->process($checkoutData);

        return redirect()->route('store.payments.start', $order);
    }
}
