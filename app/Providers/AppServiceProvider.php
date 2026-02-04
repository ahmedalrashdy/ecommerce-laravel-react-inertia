<?php

namespace App\Providers;

use App\Contracts\Payments\PaymentGatewayInterface;
use App\Events\Orders\OrderDelivered;
use App\Events\Orders\OrderPaymentSucceeded;
use App\Events\Orders\OrderShipped;
use App\Events\Returns\ReturnApproved;
use App\Events\Returns\ReturnInspected;
use App\Events\Returns\ReturnReceived;
use App\Events\Returns\ReturnShippedBack;
use App\Listeners\Auth\MergeSessionCart;
use App\Listeners\Auth\SendWelcomeEmail;
use App\Listeners\Orders\NotifyCustomerOfOrderDelivery;
use App\Listeners\Orders\NotifyCustomerOfOrderPaymentSuccess;
use App\Listeners\Orders\SendOrderShippedNotification;
use App\Listeners\Returns\NotifyCustomerOfReturnApproval;
use App\Listeners\Returns\NotifyCustomerOfReturnInspection;
use App\Listeners\Returns\NotifyCustomerOfReturnReceived;
use App\Listeners\Returns\NotifyCustomerOfReturnShippedBack;
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
use Illuminate\Foundation\Support\Providers\EventServiceProvider as BaseEventServiceProvider;
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
        BaseEventServiceProvider::disableEventDiscovery();

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
        Event::listen(Login::class, SendWelcomeEmail::class);
        Event::listen(Registered::class, SendWelcomeEmail::class);
        Event::listen(OrderPaymentSucceeded::class, NotifyCustomerOfOrderPaymentSuccess::class);
        Event::listen(OrderDelivered::class, NotifyCustomerOfOrderDelivery::class);
        Event::listen(OrderShipped::class, SendOrderShippedNotification::class);
        Event::listen(ReturnApproved::class, NotifyCustomerOfReturnApproval::class);
        Event::listen(ReturnShippedBack::class, NotifyCustomerOfReturnShippedBack::class);
        Event::listen(ReturnReceived::class, NotifyCustomerOfReturnReceived::class);
        Event::listen(ReturnInspected::class, NotifyCustomerOfReturnInspection::class);
        // if (app()->environment('production')) {
        //     URL::forceScheme('https');
        // }
    }
}
