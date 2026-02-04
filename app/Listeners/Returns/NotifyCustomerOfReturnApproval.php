<?php

namespace App\Listeners\Returns;

use App\Events\Returns\ReturnApproved;
use App\Notifications\Returns\CustomerReturnApprovedNotification;

class NotifyCustomerOfReturnApproval
{
    /**
     * Handle the event.
     */
    public function handle(ReturnApproved $event): void
    {
        $returnOrder = $event->returnOrder->loadMissing(['user', 'order']);

        if (! $returnOrder->user) {
            return;
        }

        $returnOrder->user->notify(new CustomerReturnApprovedNotification($returnOrder));
    }
}
