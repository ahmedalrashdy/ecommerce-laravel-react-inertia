<?php

namespace App\Observers;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Services\Orders\ProductSalesCounterService;
use Illuminate\Support\Facades\DB;

class OrderObserver
{
    public function __construct(private ProductSalesCounterService $salesCounter) {}

    public function updated(Order $order): void
    {
        if ($this->shouldIncrementSalesCount($order)) {
            $this->salesCounter->incrementForOrder($order);
        }

        if ($this->shouldDecrementSalesCountForCancellation($order)) {
            $this->salesCounter->decrementForCancelledOrder($order);
        }
    }

    public function created(Order $order): void
    {
        if ($order->payment_status !== PaymentStatus::PAID) {
            return;
        }

        DB::afterCommit(function () use ($order): void {
            $this->salesCounter->incrementForOrder($order);
        });
    }

    private function shouldIncrementSalesCount(Order $order): bool
    {
        if (! $order->wasChanged('payment_status')) {
            return false;
        }

        if ($order->payment_status !== PaymentStatus::PAID) {
            return false;
        }

        return $order->getOriginal('payment_status') !== PaymentStatus::PAID;
    }

    private function shouldDecrementSalesCountForCancellation(Order $order): bool
    {
        if (! $order->wasChanged('status')) {
            return false;
        }

        if ($order->status !== OrderStatus::CANCELLED) {
            return false;
        }

        if ($order->getOriginal('status') === OrderStatus::CANCELLED) {
            return false;
        }

        $paidStatuses = [
            PaymentStatus::PAID,
            PaymentStatus::REFUND_PENDING,
            PaymentStatus::PARTIALLY_REFUNDED,
            PaymentStatus::REFUNDED,
        ];

        return in_array($order->payment_status, $paidStatuses, true)
            || in_array($order->getOriginal('payment_status'), $paidStatuses, true);
    }
}
