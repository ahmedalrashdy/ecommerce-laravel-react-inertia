<?php

namespace App\Listeners\Returns;

use App\Events\Returns\ReturnReceived;
use App\Notifications\Returns\CustomerReturnReceivedNotification;

class NotifyCustomerOfReturnReceived
{
    /**
     * Handle the event.
     */
    public function handle(ReturnReceived $event): void
    {
        $returnOrder = $event->returnOrder->loadMissing(['user', 'order']);

        if (! $returnOrder->user) {
            return;
        }

        $returnOrder->user->notify(new CustomerReturnReceivedNotification($returnOrder));
    }
}
