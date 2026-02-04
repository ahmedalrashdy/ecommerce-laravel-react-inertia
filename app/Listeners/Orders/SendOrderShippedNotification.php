<?php

namespace App\Listeners\Orders;

use App\Events\Orders\OrderShipped;
use App\Notifications\Orders\OrderShippedNotification;

class SendOrderShippedNotification
{
    /**
     * Handle the event.
     */
    public function handle(OrderShipped $event): void
    {
        $order = $event->order;

        if (! $order->user) {
            return;
        }

        $order->user->notify(new OrderShippedNotification($order));
    }
}
