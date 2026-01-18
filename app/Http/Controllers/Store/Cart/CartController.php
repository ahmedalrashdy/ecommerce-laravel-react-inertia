<?php

namespace App\Http\Controllers\Store\Cart;

use App\Exceptions\OutOfStockException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Store\Cart\UpdateCartItemRequest;
use App\Models\Cart;
use App\Models\ProductVariant;
use App\Services\Cart\CartService;
use App\Traits\FlashMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    use FlashMessage;

    public function __construct(
        private CartService $cartService
    ) {}

    public function index(Request $request): Response
    {
        $cart = $this->cartService->getOrCreateCart(auth()->user(), session()->getId());

        return Inertia::render('store/cart/index', [
            'cart' => $this->cartService->cartSummary($cart),
        ]);
    }

    /**
     * Add item to cart
     */
    public function store(Request $request, ProductVariant $variant)
    {
        $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        try {
            $cart = $this->cartService->getOrCreateCart(auth()->user(), session()->getId());
            $quantity = $request->integer('quantity', 1);
            $this->cartService->addItem($cart, $variant, $quantity);
            $this->flashSuccess('تمت إضافة المنتج للسلة بنجاح');
        } catch (OutOfStockException $e) {
            $this->flashError($e->getMessage());
        }
        Cookie::queue(cookie('cartChanged', true, path: '/'));

        return back();
    }

    /**
     * Remove item from cart
     */
    public function destroy(Request $request, int $id)
    {
        $cart = $this->cartService->getOrCreateCart(auth()->user(), session()->getId());
        $this->cartService->removeItem($cart, $id);
        $this->flashSuccess('تم حذف العنصر من السلة');
        Cookie::queue(cookie('cartChanged', true, path: '/'));

        return back();
    }

    public function removeCartItemByVariantId(Request $request, ProductVariant $variant)
    {
        $cart = $this->cartService->getOrCreateCart(auth()->user(), session()->getId());
        $cart->items()->where('product_variant_id', $variant->id)->delete();
        $this->flashSuccess('تم حذف العنصر من المفضلة');
        Cookie::queue(cookie('cartChanged', true, path: '/'));

        return back();
    }

    /**
     * Update item quantity
     */
    public function updateQuantity(UpdateCartItemRequest $request, ProductVariant $variant)
    {
        try {
            $cart = $this->cartService->getOrCreateCart(auth()->user(), session()->getId());
            $this->cartService->updateQuantity(
                $cart,
                $variant,
                $request->validated()['quantity']
            );
            Cookie::queue(cookie('cartChanged', true, path: '/'));
        } catch (OutOfStockException $e) {
            $this->flashError($e->getMessage());
        }

        return back();
    }

    /**
     * Toggle item selection
     */
    public function toggleSelection(Request $request, int $id)
    {
        $cart = $this->cartService->getOrCreateCart(auth()->user(), session()->getId());
        $this->cartService->toggleSelection($cart, $id);
        Cookie::queue(cookie('cartChanged', true, path: '/'));

        return back();
    }

    /**
     * Toggle all items selection
     */
    public function toggleAll(Request $request)
    {
        $request->validate([
            'select_all' => ['required', 'boolean'],
        ]);
        $cart = $this->cartService->getOrCreateCart(auth()->user(), session()->getId());
        $this->cartService->toggleAll($cart, $request->boolean('select_all'));
        Cookie::queue(cookie('cartChanged', true, path: '/'));

        return back();
    }
}
