<?php

namespace App\Http\Controllers\Store\Checkout;

use App\Data\Basic\CartItemData;
use App\Data\Orders\AddressData;
use App\Data\Orders\OrderItemData;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserAddressResource;
use App\Services\Cart\CartService;
use App\Services\Checkout\PricingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    public function index(
        Request $request,
        CartService $cartService,
        PricingService $pricingService
    ): Response|RedirectResponse {
        $user = $request->user();
        $cart = $cartService->getOrCreateCart($user, $request->session()->getId());

        $cartItems = $cart->items()
            ->with(['productVariant.product', 'productVariant.defaultImage', 'productVariant.attributeValues.attribute'])
            ->where('is_selected', true)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('store.cart.index');
        }

        $addresses = $user->userAddresses()
            ->orderByDesc('is_default_shipping')
            ->orderByDesc('created_at')
            ->get();

        $defaultShipping = $addresses->firstWhere('is_default_shipping', true) ?? $addresses->first();
        $pricingAddress = $defaultShipping ? AddressData::fromModel($defaultShipping) : null;

        $orderItems = $cartItems->map(fn ($item) => OrderItemData::fromCartItem($item));
        $summary = $pricingService->calculateTotal($orderItems, $pricingAddress);
        $idempotencyKey = (string) Str::uuid();

        return Inertia::render('store/checkout/index', [
            'items' => CartItemData::collect($cartItems),
            'addresses' => UserAddressResource::collection($addresses),
            'defaultShippingAddressId' => $defaultShipping?->id,
            'idempotencyKey' => $idempotencyKey,
            'summary' => [
                'subtotal' => $summary->subtotal,
                'taxAmount' => $summary->tax_amount,
                'shippingCost' => $summary->shipping_cost,
                'discountAmount' => $summary->discount_amount,
                'grandTotal' => $summary->grand_total,
                'formattedSubtotal' => $summary->formatted_subtotal,
                'formattedTaxAmount' => $summary->formatted_tax_amount,
                'formattedShippingCost' => $summary->formatted_shipping_cost,
                'formattedDiscountAmount' => $summary->formatted_discount_amount,
                'formattedGrandTotal' => $summary->formatted_grand_total,
            ],
        ]);
    }
}
