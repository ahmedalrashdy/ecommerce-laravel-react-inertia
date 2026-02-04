<?php

namespace App\Services\Payments;

use App\Data\Payments\TransactionEntryData;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Events\Orders\OrderPaymentSucceeded;
use App\Models\Order;
use App\Models\Transaction;
use Brick\Money\Money;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Event;
use Stripe\StripeObject;
use Throwable;

class StripeWebhookService
{
    public function __construct(protected TransactionService $transactionService) {}

    public function handle(Event $event): void
    {
        $eventType = $event->type;

        match ($eventType) {
            'checkout.session.completed' => $this->handleCheckoutCompleted($event),
            'checkout.session.expired' => $this->handleCheckoutExpired($event),
            'payment_intent.payment_failed' => $this->handlePaymentFailed($event),
            default => null,
        };
    }

    protected function handleCheckoutCompleted(Event $event): void
    {
        $session = $event->data->object;
        if (! $session instanceof StripeObject) {
            return;
        }

        $orderId = $this->extractOrderId($session);
        if (! $orderId) {
            Log::warning('Stripe checkout completed without order reference.', [
                'event_id' => $event->id,
            ]);

            return;
        }

        DB::transaction(function () use ($event, $session, $orderId): void {
            if ($this->eventAlreadyProcessed($event->id)) {
                return;
            }

            $order = Order::lockForUpdate()->find($orderId);
            if (! $order) {
                Log::warning('Stripe checkout completed for missing order.', [
                    'event_id' => $event->id,
                    'order_id' => $orderId,
                ]);

                return;
            }

            if (! $this->amountMatchesOrder($order, $session)) {
                Log::warning('Stripe checkout amount mismatch.', [
                    'event_id' => $event->id,
                    'order_id' => $orderId,
                ]);

                return;
            }

            $paymentMethod = $this->resolvePaymentMethod($session);
            $amount = $this->amountFromSession($session);

            $this->transactionService->record(new TransactionEntryData(
                order_id: $order->id,
                user_id: $order->user_id,
                type: TransactionType::Payment,
                payment_method: $paymentMethod,
                amount: $amount,
                currency: strtoupper((string) ($session->currency ?? config('payments.currency', 'USD'))),
                status: TransactionStatus::Success,
                transaction_ref: $session->payment_intent,
                gateway_response: $event->toArray(),
                description: 'Stripe checkout payment succeeded',
                gateway: 'stripe',
                event_id: $event->id,
                event_type: $event->type,
                checkout_session_id: $session->id,
                payment_intent_id: $session->payment_intent,
            ));

            if ($order->payment_status !== PaymentStatus::PAID) {
                $order->update([
                    'payment_status' => PaymentStatus::PAID,
                    'status' => OrderStatus::PROCESSING,
                    'paid_at' => $order->paid_at ?? now(),
                    'payment_method' => $paymentMethod,
                ]);

                $order->history()->create([
                    'status' => OrderStatus::PROCESSING,
                    'comment' => 'تم الدفع بنجاح',
                    'is_visible_to_user' => true,
                    'actor_type' => get_class($order->user),
                    'actor_id' => $order->user_id,
                ]);

                event(new OrderPaymentSucceeded($order));
            }
        });
    }

    protected function handleCheckoutExpired(Event $event): void
    {
        $session = $event->data->object;
        if (! $session instanceof StripeObject) {
            return;
        }

        $orderId = $this->extractOrderId($session);
        if (! $orderId) {
            return;
        }

        DB::transaction(function () use ($event, $session, $orderId): void {
            if ($this->eventAlreadyProcessed($event->id)) {
                return;
            }

            $order = Order::lockForUpdate()->find($orderId);
            if (! $order) {
                return;
            }

            $amount = $this->amountFromSession($session);
            $paymentMethod = $this->resolvePaymentMethod($session);

            $this->transactionService->record(new TransactionEntryData(
                order_id: $order->id,
                user_id: $order->user_id,
                type: TransactionType::Payment,
                payment_method: $paymentMethod,
                amount: $amount,
                currency: strtoupper((string) ($session->currency ?? config('payments.currency', 'USD'))),
                status: TransactionStatus::Failed,
                transaction_ref: $session->payment_intent,
                gateway_response: $event->toArray(),
                description: 'Stripe checkout session expired',
                gateway: 'stripe',
                event_id: $event->id,
                event_type: $event->type,
                checkout_session_id: $session->id,
                payment_intent_id: $session->payment_intent,
            ));

            if ($order->payment_status !== PaymentStatus::PAID) {
                $order->update([
                    'payment_status' => PaymentStatus::FAILED,
                ]);

                $order->history()->create([
                    'status' => $order->status,
                    'comment' => 'انتهت جلسة الدفع بدون إكمال.',
                    'is_visible_to_user' => true,
                    'actor_type' => get_class($order->user),
                    'actor_id' => $order->user_id,
                ]);
            }
        });
    }

    protected function handlePaymentFailed(Event $event): void
    {
        $paymentIntent = $event->data->object;
        if (! $paymentIntent instanceof StripeObject) {
            return;
        }

        $metadata = $paymentIntent->metadata?->toArray() ?? [];
        $orderId = $metadata['order_id'] ?? null;

        if (! $orderId) {
            Log::warning('Stripe payment failed without order reference.', [
                'event_id' => $event->id,
            ]);

            return;
        }

        DB::transaction(function () use ($event, $paymentIntent, $orderId): void {
            if ($this->eventAlreadyProcessed($event->id)) {
                return;
            }

            $order = Order::lockForUpdate()->find($orderId);
            if (! $order) {
                return;
            }

            $amount = $this->amountFromPaymentIntent($paymentIntent);
            $paymentMethod = $this->resolvePaymentMethod($paymentIntent);

            $this->transactionService->record(new TransactionEntryData(
                order_id: $order->id,
                user_id: $order->user_id,
                type: TransactionType::Payment,
                payment_method: $paymentMethod,
                amount: $amount,
                currency: strtoupper((string) ($paymentIntent->currency ?? config('payments.currency', 'USD'))),
                status: TransactionStatus::Failed,
                transaction_ref: $paymentIntent->id,
                gateway_response: $event->toArray(),
                description: 'Stripe payment failed',
                gateway: 'stripe',
                event_id: $event->id,
                event_type: $event->type,
                payment_intent_id: $paymentIntent->id,
            ));

            if ($order->payment_status !== PaymentStatus::PAID) {
                $order->update([
                    'payment_status' => PaymentStatus::FAILED,
                ]);

                $order->history()->create([
                    'status' => $order->status,
                    'comment' => 'فشل الدفع عبر Stripe.',
                    'is_visible_to_user' => true,
                    'actor_type' => get_class($order->user),
                    'actor_id' => $order->user_id,
                ]);
            }
        });
    }

    protected function extractOrderId(StripeObject $session): ?int
    {
        $metadata = $session->metadata?->toArray() ?? [];
        $orderId = $metadata['order_id'] ?? $session->client_reference_id ?? null;

        return $orderId ? (int) $orderId : null;
    }

    protected function amountMatchesOrder(Order $order, StripeObject $session): bool
    {
        $amountTotal = (int) ($session->amount_total ?? 0);
        if ($amountTotal <= 0) {
            return false;
        }

        $currency = strtoupper((string) ($session->currency ?? config('payments.currency', 'USD')));
        if ($currency !== strtoupper((string) config('payments.currency', 'USD'))) {
            return false;
        }

        $expected = Money::of($order->grand_total, $currency)
            ->getMinorAmount()
            ->toScale(0)
            ->__toString();

        return $amountTotal === (int) $expected;
    }

    protected function amountFromSession(StripeObject $session): float
    {
        $amountTotal = (int) ($session->amount_total ?? 0);

        return $amountTotal / 100;
    }

    protected function amountFromPaymentIntent(StripeObject $paymentIntent): float
    {
        $amountTotal = (int) ($paymentIntent->amount ?? 0);

        return $amountTotal / 100;
    }

    protected function resolvePaymentMethod(StripeObject $session): PaymentMethod
    {
        $types = Arr::wrap($session->payment_method_types ?? []);

        if (in_array('card', $types, true)) {
            return PaymentMethod::CREDIT_CARD;
        }

        return PaymentMethod::PENDING;
    }

    protected function eventAlreadyProcessed(string $eventId): bool
    {
        try {
            return Transaction::query()->where('event_id', $eventId)->exists();
        } catch (Throwable) {
            return false;
        }
    }
}
