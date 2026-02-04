<?php

namespace App\Listeners\Returns;

use App\Events\Returns\ReturnInspected;
use App\Notifications\Returns\CustomerReturnInspectedNotification;

class NotifyCustomerOfReturnInspection
{
    /**
     * Handle the event.
     */
    public function handle(ReturnInspected $event): void
    {
        $returnOrder = $event->returnOrder->loadMissing(['user', 'order']);

        if (! $returnOrder->user) {
            return;
        }

        $returnOrder->user->notify(new CustomerReturnInspectedNotification($returnOrder));
    }
}
