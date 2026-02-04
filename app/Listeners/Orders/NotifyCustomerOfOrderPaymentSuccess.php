<?php

namespace App\Listeners\Orders;

use App\Events\Orders\OrderPaymentSucceeded;
use App\Notifications\Orders\CustomerOrderPaymentSucceededNotification;

class NotifyCustomerOfOrderPaymentSuccess
{
    /**
     * Handle the event.
     */
    public function handle(OrderPaymentSucceeded $event): void
    {
        $order = $event->order;

        if (! $order->user) {
            return;
        }

        $order->user->notify(new CustomerOrderPaymentSucceededNotification($order));
    }
}
