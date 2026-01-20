<?php

namespace App\Services\Payments;

use App\Contracts\Payments\PaymentGatewayInterface;
use App\Data\Payments\PaymentResultData;
use App\Data\Payments\TransactionEntryData;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\TransactionType;
use App\Models\Order;
use Brick\Money\Money;
use Illuminate\Support\Facades\DB;

class PaymentProcessingService
{
    public function __construct(
        protected PaymentGatewayInterface $gateway,
        protected TransactionService $transactionService
    ) {}

    public function processPayment(Order $order, array $paymentToken): PaymentResultData
    {
        $amount = Money::of($order->grand_total, 'USD');
        $amountValue = (float) $amount->getAmount()->toScale(2)->__toString();

        $result = $this->gateway->charge($amountValue, $paymentToken);

        // 2. تسجيل المعاملة وتحديث الطلب (Atomic)
        DB::transaction(function () use ($order, $result, $amountValue) {

            // أ. تسجيل المعاملة
            $this->transactionService->record(new TransactionEntryData(
                order_id: $order->id,
                user_id: $order->user_id, // العميل هو الدافع
                type: TransactionType::Payment, // 1
                payment_method: $order->payment_method,
                amount: $amountValue,
                currency: 'USD', // يمكن جلبها من الكونفق
                status: $result->status,
                transaction_ref: $result->transactionRef,
                gateway_response: $result->rawResponse,
                description: $result->success ? 'دفع ناجح' : 'فشل الدفع: '.$result->errorMessage
            ));

            // ب. تحديث حالة الطلب بناءً على النتيجة
            if ($result->success) {
                $order->update([
                    'payment_status' => PaymentStatus::PAID, // 2
                    'status' => OrderStatus::PROCESSING, // 2 (نبدأ التجهيز)
                    'paid_at' => now(),
                ]);
            } else {
                $order->update([
                    'payment_status' => PaymentStatus::FAILED, // 3
                    // حالة الطلب اللوجستية تبقى PENDING حتى يحاول مرة أخرى
                ]);
            }

            $order->history()->create([
                'status' => $result->success ? OrderStatus::PROCESSING : OrderStatus::PENDING,
                'comment' => $result->success ? 'تم الدفع بنجاح' : 'فشل الدفع',
                'is_visible_to_user' => true,
                'actor_type' => get_class($order->user),
                'actor_id' => $order->user_id,
            ]);
        });

        return $result;
    }
}
