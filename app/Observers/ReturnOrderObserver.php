<?php

namespace App\Observers;

use App\Enums\ReturnStatus;
use App\Models\ReturnOrder;
use App\Services\Orders\ProductSalesCounterService;

class ReturnOrderObserver
{
    public function __construct(private ProductSalesCounterService $salesCounter) {}

    public function updated(ReturnOrder $returnOrder): void
    {
        if (! $returnOrder->wasChanged('status')) {
            return;
        }

        if ($returnOrder->status !== ReturnStatus::COMPLETED) {
            return;
        }

        if ($returnOrder->getOriginal('status') === ReturnStatus::COMPLETED) {
            return;
        }

        $this->salesCounter->decrementForReturn($returnOrder);
    }
}
