<?php

namespace App\Contracts\Payments;

use App\Data\Payments\CheckoutSessionData;
use App\Data\Payments\PaymentResultData;
use App\Models\Order;

interface PaymentGatewayInterface
{
    /**
     * هل البوابة تدعم الاسترجاع التلقائي؟
     */
    public function supportsAutoRefund(): bool;

    /**
     * إنشاء جلسة دفع مستضافة (Hosted Checkout).
     *
     * @param  array<string, mixed>  $payload
     */
    public function createCheckoutSession(Order $order, array $payload): CheckoutSessionData;

    /**
     * تنفيذ عملية خصم (Charge)
     *
     * @param  float  $amount  المبلغ
     * @param  array  $paymentData  بيانات البطاقة أو التوكن (token, card_id)
     */
    public function charge(float $amount, array $paymentData): PaymentResultData;

    /**
     * تنفيذ عملية استرجاع (Refund)
     *
     * @param  string  $transactionRef  رقم العملية في البوابة
     * @param  float  $amount  المبلغ المسترد
     */
    public function refund(string $transactionRef, float $amount): PaymentResultData;
}
