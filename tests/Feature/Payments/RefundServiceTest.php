<?php

namespace Tests\Feature\Payments;

use App\Contracts\Payments\PaymentGatewayInterface;
use App\Data\Payments\CheckoutSessionData;
use App\Data\Payments\PaymentResultData;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Payments\RefundService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class RefundServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_auto_refund_records_failed_transaction_on_exception(): void
    {
        $this->app->bind(PaymentGatewayInterface::class, fn (): PaymentGatewayInterface => new class implements PaymentGatewayInterface
        {
            public function supportsAutoRefund(): bool
            {
                return true;
            }

            public function createCheckoutSession(Order $order, array $payload): CheckoutSessionData
            {
                return new CheckoutSessionData(
                    sessionId: 'session_test',
                    url: 'https://example.test/checkout',
                    paymentIntentId: null,
                    expiresAt: null,
                    rawResponse: [],
                );
            }

            public function charge(float $amount, array $paymentData): PaymentResultData
            {
                return new PaymentResultData(
                    success: true,
                    status: TransactionStatus::Success,
                    transactionRef: 'charge_ref',
                    errorMessage: null,
                    rawResponse: [],
                );
            }

            public function refund(string $transactionRef, float $amount): PaymentResultData
            {
                throw new RuntimeException('Gateway unavailable');
            }
        });

        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create([
            'grand_total' => '150.00',
        ]);

        Transaction::create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'type' => TransactionType::Payment,
            'payment_method' => $order->payment_method,
            'amount' => 150.00,
            'currency' => 'USD',
            'status' => TransactionStatus::Success,
            'transaction_ref' => 'TX-REF-001',
            'gateway_response' => [],
            'description' => 'Payment success',
        ]);

        $result = app(RefundService::class)->processAutoRefund($order);

        $this->assertFalse($result);

        $this->assertDatabaseHas('transactions', [
            'order_id' => $order->id,
            'type' => TransactionType::Refund->value,
            'status' => TransactionStatus::Failed->value,
        ]);
    }
}
