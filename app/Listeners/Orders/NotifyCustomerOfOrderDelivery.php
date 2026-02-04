<?php

namespace App\Listeners\Orders;

use App\Events\Orders\OrderDelivered;
use App\Notifications\Orders\CustomerOrderDeliveredNotification;

class NotifyCustomerOfOrderDelivery
{
    /**
     * Handle the event.
     */
    public function handle(OrderDelivered $event): void
    {
        $order = $event->order;

        if (! $order->user) {
            return;
        }

        $order->user->notify(new CustomerOrderDeliveredNotification($order));
    }
}
