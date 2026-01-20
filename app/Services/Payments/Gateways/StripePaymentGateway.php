<?php

namespace App\Services\Payments\Gateways;

use App\Contracts\Payments\PaymentGatewayInterface;
use App\Data\Payments\CheckoutSessionData;
use App\Data\Payments\PaymentResultData;
use App\Enums\TransactionStatus;
use App\Models\Order;
use Stripe\StripeClient;
use Throwable;

class StripePaymentGateway implements PaymentGatewayInterface
{
    protected StripeClient $client;

    public function __construct()
    {
        $this->client = new StripeClient(config('stripe.secret'));
    }

    public function supportsAutoRefund(): bool
    {
        return true;
    }

    public function createCheckoutSession(Order $order, array $payload): CheckoutSessionData
    {
        $session = $this->client->checkout->sessions->create([
            'mode' => 'payment',
            'line_items' => $payload['line_items'] ?? [],
            'success_url' => $payload['success_url'],
            'cancel_url' => $payload['cancel_url'],
            'client_reference_id' => (string) ($payload['client_reference_id'] ?? $order->id),
            'customer_email' => $payload['customer_email'] ?? null,
            'metadata' => $payload['metadata'] ?? [],
            'payment_intent_data' => [
                'metadata' => $payload['metadata'] ?? [],
            ],
        ], [
            'idempotency_key' => $payload['idempotency_key'] ?? null,
        ]);

        return new CheckoutSessionData(
            sessionId: $session->id,
            url: $session->url,
            paymentIntentId: $session->payment_intent,
            expiresAt: $session->expires_at,
            rawResponse: $session->toArray(),
        );
    }

    public function charge(float $amount, array $paymentData): PaymentResultData
    {
        $paymentMethodId = $paymentData['payment_method_id'] ?? null;

        if (! $paymentMethodId) {
            return new PaymentResultData(
                success: false,
                status: TransactionStatus::Failed,
                transactionRef: null,
                errorMessage: 'معرّف طريقة الدفع مطلوب.',
                rawResponse: ['error' => 'payment_method_id_missing']
            );
        }

        try {
            $intent = $this->client->paymentIntents->create([
                'amount' => (int) round($amount * 100),
                'currency' => strtolower(config('payments.currency', 'USD')),
                'payment_method' => $paymentMethodId,
                'confirm' => true,
            ], [
                'idempotency_key' => $paymentData['idempotency_key'] ?? null,
            ]);

            $success = $intent->status === 'succeeded';
            $status = $success ? TransactionStatus::Success : TransactionStatus::Failed;

            return new PaymentResultData(
                success: $success,
                status: $status,
                transactionRef: $intent->id,
                errorMessage: $success ? null : $intent->last_payment_error?->message,
                rawResponse: $intent->toArray(),
            );
        } catch (Throwable $exception) {
            return new PaymentResultData(
                success: false,
                status: TransactionStatus::Failed,
                transactionRef: null,
                errorMessage: $exception->getMessage(),
                rawResponse: ['exception' => $exception->getMessage()],
            );
        }
    }

    public function refund(string $transactionRef, float $amount): PaymentResultData
    {
        try {
            $refund = $this->client->refunds->create([
                'payment_intent' => $transactionRef,
                'amount' => (int) round($amount * 100),
            ]);

            $success = $refund->status === 'succeeded';
            $status = $success ? TransactionStatus::Success : TransactionStatus::Failed;

            return new PaymentResultData(
                success: $success,
                status: $status,
                transactionRef: $refund->id,
                errorMessage: $success ? null : $refund->failure_reason,
                rawResponse: $refund->toArray(),
            );
        } catch (Throwable $exception) {
            return new PaymentResultData(
                success: false,
                status: TransactionStatus::Failed,
                transactionRef: null,
                errorMessage: $exception->getMessage(),
                rawResponse: ['exception' => $exception->getMessage()],
            );
        }
    }
}
