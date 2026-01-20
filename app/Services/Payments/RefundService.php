<?php

namespace App\Services\Payments;

use App\Contracts\Payments\PaymentGatewayInterface;
use App\Data\Payments\TransactionEntryData;
use App\Enums\PaymentStatus;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Order;
use App\Models\OrderHistory;
// لنحصل على transaction_ref القديم
use Brick\Money\Money;
use Illuminate\Support\Facades\Log;

class RefundService
{
    public function __construct(
        protected PaymentGatewayInterface $gateway,
        protected TransactionService $transactionService
    ) {}

    public function supportsAutoRefund(): bool
    {
        return $this->gateway->supportsAutoRefund();
    }

    /**
     * محاولة استرجاع المبلغ تلقائياً عبر البوابة
     */
    public function processAutoRefund(Order $order, ?float $amount = null): bool
    {
        if (! $this->supportsAutoRefund()) {
            Log::info("البوابة لا تدعم الاسترجاع التلقائي للطلب {$order->id}.");

            return false;
        }

        // 1. البحث عن المعاملة الناجحة الأصلية للحصول على Reference
        $originalTx = $order->transactions()
            ->where('type', TransactionType::Payment)
            ->where('status', TransactionStatus::Success)
            ->latest()
            ->first();

        if (! $originalTx || ! $originalTx->transaction_ref) {
            Log::warning("لا يمكن إجراء استرجاع تلقائي للطلب {$order->id}: لا يوجد مرجع دفع.");

            return false; // يتطلب تدخلاً يدوياً
        }

        $refundAmount = $amount ?? (float) $order->grand_total;

        // 2. محاولة الاتصال بالبوابة
        try {
            $result = $this->gateway->refund(
                $originalTx->transaction_ref,
                $refundAmount
            );

            // 3. تسجيل معاملة الاسترجاع
            $this->transactionService->record(new TransactionEntryData(
                order_id: $order->id,
                user_id: auth()->id(), // أو System
                type: TransactionType::Refund,
                payment_method: $order->payment_method,
                amount: $refundAmount,
                currency: 'USD',
                status: $result->status,
                transaction_ref: $result->transactionRef,
                gateway_response: $result->rawResponse,
                description: $result->success ? 'استرجاع تلقائي ناجح' : 'فشل الاسترجاع التلقائي'
            ));

            if ($result->success) {
                $this->syncPaymentStatus($order);
                $this->recordHistory($order, __('filament.orders.refund_processed'));

                return true;
            }

        } catch (\Exception $e) {
            Log::error('خطأ أثناء الاسترجاع التلقائي: '.$e->getMessage());
            $this->transactionService->record(new TransactionEntryData(
                order_id: $order->id,
                user_id: auth()->id(),
                type: TransactionType::Refund,
                payment_method: $order->payment_method,
                amount: $refundAmount,
                currency: 'USD',
                status: TransactionStatus::Failed,
                transaction_ref: $originalTx?->transaction_ref,
                gateway_response: ['error' => $e->getMessage()],
                description: 'فشل الاسترجاع التلقائي'
            ));
        }

        return false; // فشلت العملية
    }

    /**
     * تسجيل استرجاع يدوي (قام به المحاسب)
     */
    public function processManualRefund(Order $order, string $note, float $amount): void
    {
        $this->transactionService->record(new TransactionEntryData(
            order_id: $order->id,
            user_id: auth()->id(),
            type: TransactionType::Refund,
            payment_method: $order->payment_method, // يمكن تغييره لـ Bank Transfer
            amount: $amount,
            currency: 'USD',
            status: TransactionStatus::Success,
            transaction_ref: 'MANUAL-'.time(),
            gateway_response: [],
            description: "استرجاع يدوي: $note"
        ));

        $this->syncPaymentStatus($order);
        $this->recordHistory($order, __('filament.orders.refund_processed'));
    }

    private function syncPaymentStatus(Order $order): void
    {
        $refundedAmount = $order->transactions()
            ->where('type', TransactionType::Refund)
            ->where('status', TransactionStatus::Success)
            ->get()
            ->reduce(
                fn (Money $carry, $transaction): Money => $carry->plus(Money::of($transaction->amount, 'USD')),
                Money::zero('USD')
            );

        $grandTotal = Money::of($order->grand_total, 'USD');

        if ($refundedAmount->isZero()) {
            return;
        }

        $order->update([
            'payment_status' => $refundedAmount->isGreaterThanOrEqualTo($grandTotal)
                ? PaymentStatus::REFUNDED
                : PaymentStatus::PARTIALLY_REFUNDED,
        ]);
    }

    private function recordHistory(Order $order, string $comment): void
    {
        $actor = auth()->user();

        OrderHistory::create([
            'order_id' => $order->id,
            'status' => $order->status,
            'comment' => $comment,
            'is_visible_to_user' => false,
            'actor_type' => $actor?->getMorphClass(),
            'actor_id' => $actor?->getKey(),
        ]);
    }
}
