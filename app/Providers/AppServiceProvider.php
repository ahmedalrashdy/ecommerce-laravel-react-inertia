<?php

namespace App\Providers;

use App\Contracts\Payments\PaymentGatewayInterface;
use App\Listeners\MergeSessionCart;
use App\Models\Attribute;
use App\Models\Review;
use App\Models\Wishlist;
use App\Observers\AttributeObserver;
use App\Observers\ReviewObserver;
use App\Observers\WishlistObserver;
use App\Services\Payments\Gateways\StripePaymentGateway;
use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PaymentGatewayInterface::class, function ($app): PaymentGatewayInterface {
            return match (config('payments.gateway')) {
                'stripe' => $app->make(StripePaymentGateway::class),
                default => throw new RuntimeException('بوابة الدفع المحددة غير مدعومة.'),
            };
        });
        $this->app->singleton(FakerGenerator::class, function () {
            return FakerFactory::create('ar_SA');
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();

        Attribute::observe(AttributeObserver::class);
        Review::observe(ReviewObserver::class);
        Wishlist::observe(WishlistObserver::class);
        Event::listen(Login::class, MergeSessionCart::class);
        Event::listen(Registered::class, MergeSessionCart::class);

        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
