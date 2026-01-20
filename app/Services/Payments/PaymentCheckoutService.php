<?php

namespace App\Services\Payments;

use App\Contracts\Payments\PaymentGatewayInterface;
use App\Data\Payments\CheckoutSessionData;
use App\Data\Payments\TransactionEntryData;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Brick\Money\Money;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class PaymentCheckoutService
{
    public function __construct(
        protected PaymentGatewayInterface $gateway,
        protected TransactionService $transactionService
    ) {}

    public function startCheckout(Order $order): CheckoutSessionData
    {
        $order->loadMissing(['items', 'user']);

        if ($order->payment_status === PaymentStatus::PAID) {
            throw new RuntimeException('هذا الطلب مدفوع بالفعل.');
        }

        if ($order->status === OrderStatus::CANCELLED) {
            throw new RuntimeException(__('filament.orders.order_already_cancelled'));
        }

        if ($order->items->isEmpty()) {
            throw new RuntimeException('لا يمكن بدء الدفع بدون عناصر في الطلب.');
        }

        $pendingAttempt = $this->latestPendingAttempt($order);

        if ($pendingAttempt && $this->isCheckoutSessionActive($pendingAttempt)) {
            return new CheckoutSessionData(
                sessionId: $pendingAttempt->checkout_session_id ?? '',
                url: $pendingAttempt->gateway_response['url'] ?? '',
                paymentIntentId: $pendingAttempt->payment_intent_id,
                expiresAt: $pendingAttempt->gateway_response['expires_at'] ?? null,
                rawResponse: $pendingAttempt->gateway_response ?? [],
            );
        }

        $payload = $this->buildCheckoutPayload($order);
        $session = $this->gateway->createCheckoutSession($order, $payload);

        $this->transactionService->record(new TransactionEntryData(
            order_id: $order->id,
            user_id: $order->user_id,
            type: TransactionType::Payment,
            payment_method: PaymentMethod::PENDING,
            amount: (float) $order->grand_total,
            currency: $payload['currency'],
            status: TransactionStatus::Pending,
            transaction_ref: $session->sessionId,
            gateway_response: $session->rawResponse,
            description: 'Stripe checkout session created',
            gateway: 'stripe',
            idempotency_key: $payload['idempotency_key'],
            checkout_session_id: $session->sessionId,
            payment_intent_id: $session->paymentIntentId,
        ));

        return $session;
    }

    protected function buildCheckoutPayload(Order $order): array
    {
        $currency = strtolower(config('payments.currency', 'USD'));

        $lineItems = $order->items->map(function (OrderItem $item) use ($currency): array {
            $unitAmount = $this->toMinorAmount($item->price, $currency);

            return [
                'price_data' => [
                    'currency' => $currency,
                    'unit_amount' => $unitAmount,
                    'product_data' => $this->buildProductData($item),
                ],
                'quantity' => $item->quantity,
            ];
        })->all();

        $shippingAmount = $this->toMinorAmount($order->shipping_cost, $currency);
        if ($shippingAmount > 0) {
            $lineItems[] = $this->summaryLineItem('الشحن', $shippingAmount, $currency);
        }

        $taxAmount = $this->toMinorAmount($order->tax_amount, $currency);
        if ($taxAmount > 0) {
            $lineItems[] = $this->summaryLineItem('الضريبة', $taxAmount, $currency);
        }

        return [
            'currency' => $currency,
            'line_items' => $lineItems,
            'success_url' => route('store.payments.success', $order),
            'cancel_url' => route('store.payments.failed', $order),
            'client_reference_id' => (string) $order->id,
            'customer_email' => $order->user?->email,
            'metadata' => [
                'order_id' => (string) $order->id,
                'order_number' => $order->order_number,
                'user_id' => (string) $order->user_id,
            ],
            'idempotency_key' => $this->idempotencyKey($order),
        ];
    }

    protected function summaryLineItem(string $label, int $amount, string $currency): array
    {
        return [
            'price_data' => [
                'currency' => $currency,
                'unit_amount' => $amount,
                'product_data' => [
                    'name' => $label,
                ],
            ],
            'quantity' => 1,
        ];
    }

    protected function buildProductData(OrderItem $item): array
    {
        $productData = [
            'name' => $item->product_name,
        ];

        $description = $this->formatAttributes($item->attributes_list ?? []);
        if ($description !== '') {
            $productData['description'] = $description;
        }

        $imageUrl = $this->resolveImageUrl(
            $item->product_variant_snapshot['variant']['default_image'] ?? null
        );
        if ($imageUrl) {
            $productData['images'] = [$imageUrl];
        }

        return $productData;
    }

    /**
     * @param  array<int, array{name?: string, value?: string}>  $attributes
     */
    protected function formatAttributes(array $attributes): string
    {
        $parts = [];

        foreach ($attributes as $attribute) {
            $name = $attribute['name'] ?? null;
            $value = $attribute['value'] ?? null;

            if (is_string($name) && $name !== '' && is_string($value) && $value !== '') {
                $parts[] = $name.': '.$value;
            }
        }

        return implode(' | ', $parts);
    }

    protected function resolveImageUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        $storageUrl = Storage::url($path);
        if (Str::startsWith($storageUrl, ['http://', 'https://'])) {
            return $storageUrl;
        }

        return url($storageUrl);
    }

    protected function latestPendingAttempt(Order $order): ?Transaction
    {
        return Transaction::query()
            ->where('order_id', $order->id)
            ->where('type', TransactionType::Payment)
            ->where('status', TransactionStatus::Pending)
            ->whereNotNull('checkout_session_id')
            ->latest('id')
            ->first();
    }

    protected function isCheckoutSessionActive(Transaction $transaction): bool
    {
        $expiresAt = $transaction->gateway_response['expires_at'] ?? null;
        $url = $transaction->gateway_response['url'] ?? null;
        if (! $expiresAt || ! $url) {
            return false;
        }

        return now()->timestamp < (int) $expiresAt;
    }

    protected function idempotencyKey(Order $order): string
    {
        return 'order-'.$order->id.'-attempt-'.Str::uuid();
    }

    protected function toMinorAmount(string $amount, string $currency): int
    {
        $money = Money::of($amount, strtoupper($currency));

        return (int) $money->getMinorAmount()->toScale(0)->__toString();
    }
}
