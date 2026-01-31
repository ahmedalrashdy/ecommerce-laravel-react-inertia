<?php

use App\Http\Controllers\Payments\StripeWebhookController;
use App\Http\Controllers\Store\Account\AccountOverviewController;
use App\Http\Controllers\Store\Account\Addresses\AddressController;
use App\Http\Controllers\Store\Account\NotificationPreferencesController;
use App\Http\Controllers\Store\Account\Orders\OrderReorderController;
use App\Http\Controllers\Store\Account\Orders\OrderReturnController;
use App\Http\Controllers\Store\Account\Orders\OrderReturnPageController;
use App\Http\Controllers\Store\Account\Orders\OrderReviewController;
use App\Http\Controllers\Store\Account\Orders\OrdersController;
use App\Http\Controllers\Store\Account\Orders\ReturnDetailsController;
use App\Http\Controllers\Store\Account\ProfileController;
use App\Http\Controllers\Store\Cart\CartController;
use App\Http\Controllers\Store\Checkout\CheckoutController;
use App\Http\Controllers\Store\Checkout\PlaceOrderController;
use App\Http\Controllers\Store\Home\HomeController;
use App\Http\Controllers\Store\Home\ProductController;
use App\Http\Controllers\Store\Legal\LegalController;
use App\Http\Controllers\Store\Marketing\NewsletterController;
use App\Http\Controllers\Store\Marketing\StockNotificationController;
use App\Http\Controllers\Store\Payments\OrderPaymentController;
use App\Http\Controllers\Store\Payments\PaymentStatusController;
use App\Http\Controllers\Store\Search\SearchController;
use App\Http\Controllers\Store\Support\SupportController;
use App\Http\Controllers\Store\Wishlist\WishlistController;
use App\Http\Middleware\EnsureIdempotencyKey;
use App\Http\Middleware\HandleInertiaRequests;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

// store
\Route::prefix('/')
    ->middleware([HandleInertiaRequests::class])
    ->name('store.')

    ->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('home');
        Route::get('/about', [SupportController::class, 'about'])->name('about');
        Route::get('/contact', [SupportController::class, 'contact'])->name('contact');
        Route::get('/help', [SupportController::class, 'help'])->name('help');
        Route::get('/privacy', [LegalController::class, 'privacy'])->name('privacy');
        Route::get('/terms', [LegalController::class, 'terms'])->name('terms');
        Route::get('/returns', [SupportController::class, 'returns'])->name('returns');

        // Search routes
        Route::get('/search/suggestions', [SearchController::class, 'suggestions'])
            ->name('search.suggestions')->middleware('throttle:60,1');

        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/category/{category:slug}', [ProductController::class, 'index'])->name('category.show');
        Route::get('/brand/{brand:slug}', [ProductController::class, 'index'])->name('brand.show');
        Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
        Route::get('/products/{product:slug}/variants', [ProductController::class, 'variants'])->name('products.variants');

        // Cart routes
        Route::prefix('/cart')->name('cart.')->group(function () {
            Route::get('/', [CartController::class, 'index'])->name('index');
            Route::post('/add/{variant}', [CartController::class, 'store'])->name('store');
            Route::get('/dropdown', [CartController::class, 'dropdown'])->name('dropdown');
            Route::delete('/items/{id}', [CartController::class, 'destroy'])->name('items.destroy');
            Route::delete('/remove/{variant}', [CartController::class, 'removeCartItemByVariantId'])->name('items.destroyByVariantId');
            Route::patch('/items/{variant}/quantity', [CartController::class, 'updateQuantity'])->name('items.updateQuantity');
            Route::patch('/items/{id}/toggle-selection', [CartController::class, 'toggleSelection'])->name('items.toggleSelection');
            Route::patch('/toggle-all', [CartController::class, 'toggleAll'])->name('toggleAll');
        });

        // Stock Notification routes
        Route::prefix('store/stock-notifications')->middleware('auth')->name('stock-notifications.')->group(function () {
            Route::post('/', [StockNotificationController::class, 'store'])->name('store');
        });

        // Newsletter routes
        Route::prefix('store/newsletter')->name('newsletter.')->group(function () {
            Route::post('/subscribe', [NewsletterController::class, 'store'])->name('subscribe');
        });
        // Wishlist routes
        Route::prefix('/wishlist')->middleware('auth')->name('wishlist.')->group(function () {
            Route::get('/', [WishlistController::class, 'index'])->name('index');
            Route::get('/dropdown', [WishlistController::class, 'dropdown'])->name('dropdown');
            Route::post('/toggle/{variant}', [WishlistController::class, 'toggle'])->name('toggle');
        });

        // checkout and payments
        Route::middleware('auth')->group(function () {
            Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
            Route::post('/checkout/place-order', PlaceOrderController::class)
                ->middleware(EnsureIdempotencyKey::class)
                ->name('checkout.place-order');
            Route::get('/payments/{order}/start', [OrderPaymentController::class, 'start'])->name('payments.start');
            Route::get('/payments/{order}/success', [PaymentStatusController::class, 'success'])->name('payments.success');
            Route::get('/payments/{order}/failed', [PaymentStatusController::class, 'failed'])->name('payments.failed');
        });

        // Account routes
        Route::prefix('/account')->middleware('auth')->name('account.')->group(function () {
            Route::get('/', AccountOverviewController::class)->name('index');

            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

            Route::get('/notifications', [NotificationPreferencesController::class, 'edit'])
                ->name('notifications.edit');
            Route::patch('/notifications', [NotificationPreferencesController::class, 'update'])
                ->name('notifications.update');

            // Addresses routes
            Route::prefix('/addresses')->name('addresses.')->group(function () {
                Route::get('/', [AddressController::class, 'index'])->name('index');
                Route::get('/create', [AddressController::class, 'create'])->name('create');
                Route::post('/', [AddressController::class, 'store'])->name('store');
                Route::put('/{address}', [AddressController::class, 'update'])->name('update');
                Route::delete('/{address}', [AddressController::class, 'destroy'])->name('destroy');
                Route::patch('/{address}/set-default-shipping', [AddressController::class, 'setDefaultShipping'])->name('set-default-shipping');
            });

            // Orders routes
            Route::prefix('/orders')->name('orders.')->group(function () {
                Route::get('/', [OrdersController::class, 'index'])->name('index');
                Route::get('/{order}', [OrdersController::class, 'show'])->name('show');
                Route::post('/{order}/reorder', OrderReorderController::class)->name('reorder');
                Route::get('/{order}/returns', [OrderReturnPageController::class, 'show'])->name('returns.show');
                Route::post('/{order}/returns', OrderReturnController::class)->name('returns');
                Route::post('/{order}/reviews', OrderReviewController::class)->name('reviews');
            });

            // Returns routes
            Route::get('/returns/{returnOrder}', [ReturnDetailsController::class, 'show'])->name('returns.show');
        });
    });
Route::get('test', function () {
    // Product::query()->doesntHave('defaultVariant')->forceDelete();
    return 'done';
});
Route::post('/payments/webhook/stripe', StripeWebhookController::class)
    ->middleware('throttle:60,1')
    ->name('stripe.webhook');
