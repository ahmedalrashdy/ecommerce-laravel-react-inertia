<?php

namespace App\Http\Middleware;

use App\Data\Basic\BrandData;
use App\Data\Basic\CategoryData;
use App\Helpers\SettingsHelper;
use App\Models\Brand;
use App\Models\Category;
use App\Services\Cart\CartService;
use App\Services\Wishlist\WishlistService;
use Spatie\LaravelSettings\Exceptions\MissingSettings;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    public function __construct(private CartService $cartService, private WishlistService $wishlistService)
    {
    }

    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $cartChanged = $request->hasCookie('cartChanged');
        $wishlistChanged = $request->hasCookie('wishlistChanged');
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $request->user(),
            ],
            'sidebarOpen' => !$request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',

            'auth.user' => fn() => $request->user(),
            'cart' => Inertia::optional(function () {
                $cart = $this->cartService->getOrCreateCart(auth()->user(), session()->getId());

                return $this->cartService->cartSummary($cart);
            })->once()->fresh($cartChanged),
            'wishlistDropdown' => Inertia::optional(function () {
                $user = auth()->user();

                return $user ? $this->wishlistService->wishlistDropdownSummary($user) : [];
            }),
        ];
    }

    public function shareOnce(Request $request): array
    {
        $cartChanged = $request->hasCookie('cartChanged');
        $wishlistChanged = $request->hasCookie('wishlistChanged');

        return array_merge(parent::shareOnce($request), [
            'mainCategories' => function () {
                $query = Category::withDepth()->defaultOrder();

                $cats = DB::connection()->getDriverName() === 'sqlite'
                    ? $query->get()->toTree()
                    : $query->having('depth', '<=', 2)->get()->toTree();

                return CategoryData::collect($cats);
            },
            'featuredBrands' => function () {
                return BrandData::collect(Brand::featured()->published()->get());
            },
            'cartVariantIds' => Inertia::once(fn() => $this->cartService->getCartVariantIds(auth()->user(), session()->getId()))
                ->fresh($cartChanged),
            'wishlistVariantIds' => Inertia::once(function () {
                $user = auth()->user();

                return $user ? $this->wishlistService->wishlistVariantIds($user) : [];
            })
                ->fresh($wishlistChanged),
            'settings' => Inertia::once(function () {
                return [
                    'general' => [
                        'store_name' => SettingsHelper::storeName(),
                        'store_description' => SettingsHelper::storeDescription(),
                        'store_logo' => SettingsHelper::storeLogo(),
                        'store_tagline' => SettingsHelper::storeTagline(),
                    ],
                    'contact' => [
                        'phone' => SettingsHelper::contactPhone(),
                        'email' => SettingsHelper::contactEmail(),
                        'address' => SettingsHelper::contactAddress(),
                        'city' => SettingsHelper::contactCity(),
                        'country' => SettingsHelper::contactCountry(),
                    ],
                    'social' => [
                        'facebook_url' => SettingsHelper::facebookUrl(),
                        'twitter_url' => SettingsHelper::twitterUrl(),
                        'instagram_url' => SettingsHelper::instagramUrl(),
                        'youtube_url' => SettingsHelper::youtubeUrl(),
                        'linkedin_url' => SettingsHelper::linkedinUrl(),
                    ],
                ];
            }),
        ]);
    }
}
