<?php

namespace App\Listeners\Returns;

use App\Events\Returns\ReturnShippedBack;
use App\Notifications\Returns\CustomerReturnShippedBackNotification;

class NotifyCustomerOfReturnShippedBack
{
    /**
     * Handle the event.
     */
    public function handle(ReturnShippedBack $event): void
    {
        $returnOrder = $event->returnOrder->loadMissing(['user', 'order']);

        if (! $returnOrder->user) {
            return;
        }

        $returnOrder->user->notify(new CustomerReturnShippedBackNotification($returnOrder));
    }
}
