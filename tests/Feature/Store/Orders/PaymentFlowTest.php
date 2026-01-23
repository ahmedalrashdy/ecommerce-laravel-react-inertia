<?php

namespace Tests\Feature\Store\Orders;

use App\Contracts\Payments\PaymentGatewayInterface;
use App\Data\Payments\CheckoutSessionData;
use App\Enums\AttributeType;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\ProductStatus;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Image;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\Payments\StripeWebhookService;
use Brick\Money\Money;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Stripe\Event;
use Tests\TestCase;

class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_is_redirected_to_stripe_checkout(): void
    {
        $gateway = new class implements PaymentGatewayInterface
        {
            public array $payloads = [];

            public function supportsAutoRefund(): bool
            {
                return true;
            }

            public function createCheckoutSession(Order $order, array $payload): CheckoutSessionData
            {
                $this->payloads[] = $payload;

                return new CheckoutSessionData(
                    sessionId: 'cs_test_123',
                    url: 'https://checkout.test/session',
                    paymentIntentId: 'pi_test_123',
                    expiresAt: now()->addMinutes(30)->timestamp,
                    rawResponse: [
                        'id' => 'cs_test_123',
                        'url' => 'https://checkout.test/session',
                        'expires_at' => now()->addMinutes(30)->timestamp,
                    ],
                );
            }

            public function charge(float $amount, array $paymentData): \App\Data\Payments\PaymentResultData
            {
                return new \App\Data\Payments\PaymentResultData(
                    success: true,
                    status: TransactionStatus::Success,
                    transactionRef: 'charge_ref',
                    errorMessage: null,
                    rawResponse: [],
                );
            }

            public function refund(string $transactionRef, float $amount): \App\Data\Payments\PaymentResultData
            {
                return new \App\Data\Payments\PaymentResultData(
                    success: true,
                    status: TransactionStatus::Success,
                    transactionRef: 'refund_ref',
                    errorMessage: null,
                    rawResponse: [],
                );
            }
        };

        $this->app->instance(PaymentGatewayInterface::class, $gateway);

        $user = User::factory()->create();
        $order = $this->createOrderWithItem($user, '100.00', '15.00', '5.00');

        $response = $this->actingAs($user)->get(route('store.payments.start', $order));

        $response->assertRedirect('https://checkout.test/session');

        $payload = $gateway->payloads[0] ?? [];
        $this->assertNotEmpty($payload);
        $lineItems = $payload['line_items'] ?? [];

        $this->assertCount(3, $lineItems);
        $this->assertTrue($this->lineItemExists($lineItems, 'الشحن', 1500, 'usd'));
        $this->assertTrue($this->lineItemExists($lineItems, 'الضريبة', 500, 'usd'));

        $productItem = $this->findLineItemByName($lineItems, 'Test Product');
        $this->assertNotNull($productItem);

        $productData = $productItem['price_data']['product_data'] ?? [];
        $this->assertSame('Test Product', $productData['name'] ?? null);
        $this->assertSame('Color: Red', $productData['description'] ?? null);
        $this->assertSame([$this->expectedImageUrl('products/test.jpg')], $productData['images'] ?? null);

        $this->assertDatabaseHas('transactions', [
            'order_id' => $order->id,
            'type' => TransactionType::Payment->value,
            'status' => TransactionStatus::Pending->value,
            'transaction_ref' => 'cs_test_123',
        ]);
    }

    public function test_payment_success_page_loads(): void
    {
        $user = User::factory()->create();
        $order = $this->createOrderWithItem($user, '100.00');

        $response = $this->actingAs($user)->get(route('store.payments.success', $order));

        $response->assertStatus(200);
        $response->assertInertia(
            fn ($page) => $page
                ->component('store/payments/success')
                ->where('order.id', $order->id)
                ->where('order.orderNumber', $order->order_number)
        );
    }

    public function test_webhook_failed_records_payment_method_when_available(): void
    {
        $user = User::factory()->create();
        $order = $this->createOrderWithItem($user, '100.00');

        $event = Event::constructFrom([
            'id' => 'evt_test_failed',
            'type' => 'payment_intent.payment_failed',
            'data' => [
                'object' => [
                    'id' => 'pi_test_failed',
                    'object' => 'payment_intent',
                    'amount' => 10000,
                    'currency' => 'usd',
                    'payment_method_types' => ['card'],
                    'metadata' => [
                        'order_id' => (string) $order->id,
                    ],
                ],
            ],
        ]);

        app(StripeWebhookService::class)->handle($event);

        $order->refresh();

        $this->assertSame(PaymentStatus::FAILED, $order->payment_status);
        $this->assertDatabaseHas('transactions', [
            'order_id' => $order->id,
            'type' => TransactionType::Payment->value,
            'status' => TransactionStatus::Failed->value,
            'payment_method' => PaymentMethod::CREDIT_CARD->value,
            'event_id' => 'evt_test_failed',
        ]);
    }

    public function test_webhook_success_updates_order_and_records_transaction(): void
    {
        $user = User::factory()->create();
        $order = $this->createOrderWithItem($user, '100.00');

        $event = Event::constructFrom([
            'id' => 'evt_test_123',
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_test_123',
                    'object' => 'checkout.session',
                    'amount_total' => 10000,
                    'currency' => 'usd',
                    'payment_intent' => 'pi_test_123',
                    'payment_method_types' => ['card'],
                    'metadata' => [
                        'order_id' => (string) $order->id,
                        'order_number' => $order->order_number,
                        'user_id' => (string) $user->id,
                    ],
                ],
            ],
        ]);

        app(StripeWebhookService::class)->handle($event);

        $order->refresh();

        $this->assertSame(OrderStatus::PROCESSING, $order->status);
        $this->assertSame(PaymentStatus::PAID, $order->payment_status);
        $this->assertSame(PaymentMethod::CREDIT_CARD, $order->payment_method);

        $this->assertDatabaseHas('transactions', [
            'order_id' => $order->id,
            'type' => TransactionType::Payment->value,
            'status' => TransactionStatus::Success->value,
            'event_id' => 'evt_test_123',
        ]);
    }

    private function createOrderWithItem(
        User $user,
        string $itemPrice,
        string $shippingCost = '0.00',
        string $taxAmount = '0.00',
        string $discountAmount = '0.00'
    ): Order {
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => null,
            'image_path' => 'categories/test.jpg',
            'status' => 0,
            'products_count' => 0,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'brand_id' => null,
            'name' => 'Test Product',
            'slug' => 'test-product',
            'description' => null,
            'status' => ProductStatus::Published,
            'featured' => false,
            'specifications' => null,
            'sales_count' => 0,
            'favorites_count' => 0,
            'rating_avg' => 0,
            'reviews_count' => 0,
            'variants_count' => 1,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'SKU-TEST-001',
            'price' => $itemPrice,
            'compare_at_price' => null,
            'quantity' => 10,
            'is_default' => true,
        ]);

        $attribute = Attribute::create([
            'name' => 'Color',
            'type' => AttributeType::Text,
        ]);

        $attributeValue = AttributeValue::create([
            'attribute_id' => $attribute->id,
            'value' => 'Red',
            'color_code' => null,
        ]);

        $variant->attributeValues()->attach($attributeValue->id, [
            'attribute_id' => $attribute->id,
        ]);

        Image::create([
            'path' => 'products/test.jpg',
            'alt_text' => null,
            'imageable_id' => $variant->id,
            'imageable_type' => ProductVariant::class,
            'display_order' => 1,
        ]);

        $grandTotal = Money::of($itemPrice, 'USD')
            ->plus(Money::of($shippingCost, 'USD'))
            ->plus(Money::of($taxAmount, 'USD'))
            ->minus(Money::of($discountAmount, 'USD'))
            ->getAmount()
            ->toScale(2)
            ->__toString();

        $order = Order::factory()->for($user)->create([
            'payment_status' => PaymentStatus::PENDING,
            'payment_method' => PaymentMethod::PENDING,
            'status' => OrderStatus::PENDING,
            'subtotal' => $itemPrice,
            'tax_amount' => $taxAmount,
            'shipping_cost' => $shippingCost,
            'discount_amount' => $discountAmount,
            'grand_total' => $grandTotal,
        ]);

        $order->items()->create([
            'product_variant_id' => $variant->id,
            'product_id' => $product->id,
            'product_variant_snapshot' => OrderItem::createVariantSnapshot($variant),
            'price' => $itemPrice,
            'quantity' => 1,
            'discount_amount' => '0.00',
        ]);

        return $order;
    }

    private function lineItemExists(array $lineItems, string $label, int $amount, string $currency): bool
    {
        foreach ($lineItems as $item) {
            $name = $item['price_data']['product_data']['name'] ?? null;
            $unitAmount = $item['price_data']['unit_amount'] ?? null;
            $itemCurrency = $item['price_data']['currency'] ?? null;

            if ($name === $label && $unitAmount === $amount && $itemCurrency === $currency) {
                return true;
            }
        }

        return false;
    }

    private function findLineItemByName(array $lineItems, string $name): ?array
    {
        foreach ($lineItems as $item) {
            $itemName = $item['price_data']['product_data']['name'] ?? null;

            if ($itemName === $name) {
                return $item;
            }
        }

        return null;
    }

    private function expectedImageUrl(string $path): string
    {
        $storageUrl = Storage::url($path);

        if (str_starts_with($storageUrl, 'http://') || str_starts_with($storageUrl, 'https://')) {
            return $storageUrl;
        }

        return url($storageUrl);
    }
}
