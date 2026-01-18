<?php

namespace App\Http\Controllers\Store\Wishlist;

use App\Data\Basic\ProductData;
use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Services\Wishlist\WishlistService;
use App\Traits\FlashMessage;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Inertia\Inertia;
use Inertia\Response;

class WishlistController extends Controller
{
    use FlashMessage;

    public function __construct(
        private WishlistService $wishlistService
    ) {}

    /**
     * Display the wishlist page.
     */
    public function index(Request $request): Response
    {

        return Inertia::render('store/wishlist/index', [
            'products' => function () {
                $wishlists = Auth::user()
                    ->wishlists()
                    ->with(['productVariant.defaultImage', 'productVariant.product'])
                    ->get();

                return $wishlists->map(fn ($wishlist) => ProductData::fromWishlistItem($wishlist));

            },
        ]);
    }

    /**
     * Add item to wishlist
     */
    public function toggle(Request $request, ProductVariant $variant)
    {
        $user = $request->user();
        $isCreated = $this->wishlistService->toggleItem($user, $variant);
        $message = 'تم إزالة المنتج من السلة بنجاح';
        if ($isCreated) {
            $message = 'تمت إضافة المنتج للمفضلة';

        }
        $this->flashSuccess($message);
        Cookie::queue(cookie('wishlistChanged', true, path: '/'));

        return back();
    }
}
