<?php

namespace Tests\Feature\Filament\Orders;

use App\Enums\CancelRefundOption;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\StockMovementType;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Filament\Resources\OrdersManagement\Orders\OrderResource;
use App\Filament\Resources\OrdersManagement\Orders\Pages\CancelOrder;
use App\Filament\Resources\OrdersManagement\Orders\Pages\CreateManualOrder;
use App\Filament\Resources\OrdersManagement\Orders\Pages\ListCancelledOrders;
use App\Filament\Resources\OrdersManagement\Orders\Pages\ListOrders;
use App\Filament\Resources\OrdersManagement\Orders\Pages\ListPendingOrders;
use App\Filament\Resources\OrdersManagement\Orders\Pages\ManualRefundOrder;
use App\Filament\Resources\OrdersManagement\Orders\Pages\ViewOrder;
use App\Filament\Resources\OrdersManagement\Orders\RelationManagers\HistoriesRelationManager;
use App\Filament\Resources\OrdersManagement\Orders\RelationManagers\ItemsRelationManager;
use App\Filament\Resources\OrdersManagement\Orders\RelationManagers\StockMovementsRelationManager;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserAddress;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OrderResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_orders_list(): void
    {
        $this->actingAs($user = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($user);
        Filament::setCurrentPanel('admin');

        $orders = Order::factory()->count(2)->create();

        Livewire::test(ListOrders::class)
            ->assertCanSeeTableRecords($orders);
    }

    public function test_admin_can_view_pending_payment_orders_list(): void
    {
        $this->actingAs($user = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($user);
        Filament::setCurrentPanel('admin');

        $pending = Order::factory()->create([
            'status' => OrderStatus::PENDING,
        ]);

        $processing = Order::factory()->create([
            'status' => OrderStatus::PROCESSING,
        ]);

        Livewire::test(ListPendingOrders::class)
            ->assertCanSeeTableRecords([$pending])
            ->assertCanNotSeeTableRecords([$processing]);
    }

    public function test_admin_can_view_cancelled_orders_list(): void
    {
        $this->actingAs($user = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($user);
        Filament::setCurrentPanel('admin');

        $cancelled = Order::factory()->create([
            'status' => OrderStatus::CANCELLED,
        ]);

        $processing = Order::factory()->create([
            'status' => OrderStatus::PROCESSING,
        ]);

        Livewire::test(ListCancelledOrders::class)
            ->assertCanSeeTableRecords([$cancelled])
            ->assertCanNotSeeTableRecords([$processing]);
    }

    public function test_admin_can_view_order_details(): void
    {
        $this->actingAs($user = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($user);
        Filament::setCurrentPanel('admin');

        $order = Order::factory()->create();

        Livewire::test(ViewOrder::class, [
            'record' => $order->getRouteKey(),
        ])->assertSee($order->order_number);
    }

    public function test_admin_can_view_order_history_records(): void
    {
        $this->actingAs($user = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($user);
        Filament::setCurrentPanel('admin');

        $order = Order::factory()->create();
        $history = OrderHistory::factory()->for($order)->create();

        Livewire::test(HistoriesRelationManager::class, [
            'ownerRecord' => $order,
            'pageClass' => ViewOrder::class,
        ])->assertCanSeeTableRecords([$history]);
    }

    public function test_order_resource_does_not_register_edit_page(): void
    {
        $this->assertArrayNotHasKey('edit', OrderResource::getPages());
    }

    public function test_admin_can_search_order_items_by_snapshot(): void
    {
        $this->actingAs($user = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($user);
        Filament::setCurrentPanel('admin');

        $variant = $this->createProductVariant(quantity: 3, price: '120.00');
        $order = Order::factory()->create();

        $item = OrderItem::create([
            'order_id' => $order->id,
            'product_variant_id' => $variant->id,
            'product_id' => $variant->product_id,
            'product_variant_snapshot' => OrderItem::createVariantSnapshot($variant),
            'price' => '120.00',
            'quantity' => 2,
            'discount_amount' => '0.00',
        ]);

        Livewire::test(ItemsRelationManager::class, [
            'ownerRecord' => $order,
            'pageClass' => ViewOrder::class,
        ])
            ->searchTable('Test Product')
            ->assertCanSeeTableRecords([$item])
            ->sortTable('product_name')
            ->assertCanSeeTableRecords([$item]);

        Livewire::test(ItemsRelationManager::class, [
            'ownerRecord' => $order,
            'pageClass' => ViewOrder::class,
        ])
            ->searchTable($variant->sku)
            ->assertCanSeeTableRecords([$item]);
    }

    public function test_admin_can_see_item_attributes_from_snapshot(): void
    {
        $this->actingAs($user = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($user);
        Filament::setCurrentPanel('admin');

        $variant = $this->createProductVariant(quantity: 3, price: '120.00');
        $order = Order::factory()->create();

        OrderItem::create([
            'order_id' => $order->id,
            'product_variant_id' => $variant->id,
            'product_id' => $variant->product_id,
            'product_variant_snapshot' => [
                'product' => [
                    'name' => 'Snapshot Product',
                ],
                'variant' => [
                    'sku' => 'SNAP-1',
                    'attributes' => [
                        ['name' => 'Color', 'value' => 'Red'],
                        ['name' => 'Size', 'value' => 'M'],
                    ],
                ],
            ],
            'price' => '120.00',
            'quantity' => 1,
            'discount_amount' => '0.00',
        ]);

        Livewire::test(ItemsRelationManager::class, [
            'ownerRecord' => $order,
            'pageClass' => ViewOrder::class,
        ])
            ->assertSee('Color: Red')
            ->assertSee('Size: M');
    }

    public function test_admin_can_view_stock_movements_for_order(): void
    {
        $this->actingAs($user = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($user);
        Filament::setCurrentPanel('admin');

        $variant = $this->createProductVariant(quantity: 3, price: '120.00');
        $order = Order::factory()->create();

        $movement = StockMovement::create([
            'product_variant_id' => $variant->id,
            'type' => StockMovementType::SALE,
            'quantity' => -2,
            'quantity_before' => 3,
            'quantity_after' => 1,
            'sourceable_type' => Order::class,
            'sourceable_id' => $order->id,
            'description' => 'Order movement',
        ]);

        Livewire::test(StockMovementsRelationManager::class, [
            'ownerRecord' => $order,
            'pageClass' => ViewOrder::class,
        ])->assertCanSeeTableRecords([$movement]);
    }

    public function test_admin_can_mark_order_as_processing(): void
    {
        $this->actingAs($user = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($user);
        Filament::setCurrentPanel('admin');

        $order = Order::factory()->create([
            'status' => OrderStatus::PENDING,
            'payment_status' => PaymentStatus::PAID,
        ]);

        Livewire::test(ViewOrder::class, [
            'record' => $order->getRouteKey(),
        ])->callAction('mark_processing');

        $order->refresh();

        $this->assertSame(OrderStatus::PROCESSING, $order->status);
        $this->assertDatabaseHas('order_histories', [
            'order_id' => $order->id,
            'status' => OrderStatus::PROCESSING->value,
        ]);
    }

    public function test_admin_can_mark_order_as_shipped(): void
    {
        $this->actingAs($user = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($user);
        Filament::setCurrentPanel('admin');

        $order = Order::factory()->create([
            'status' => OrderStatus::PROCESSING,
            'payment_status' => PaymentStatus::PAID,
        ]);

        Livewire::test(ViewOrder::class, [
            'record' => $order->getRouteKey(),
        ])->callAction('mark_shipped', ['tracking_number' => 'TRK-123']);

        $order->refresh();

        $this->assertSame(OrderStatus::SHIPPED, $order->status);
        $this->assertSame('TRK-123', $order->tracking_number);
        $this->assertDatabaseHas('order_histories', [
            'order_id' => $order->id,
            'status' => OrderStatus::SHIPPED->value,
        ]);
    }

    public function test_admin_can_mark_order_as_delivered(): void
    {
        $this->actingAs($user = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($user);
        Filament::setCurrentPanel('admin');

        $order = Order::factory()->create([
            'status' => OrderStatus::SHIPPED,
            'payment_status' => PaymentStatus::PAID,
        ]);

        Livewire::test(ViewOrder::class, [
            'record' => $order->getRouteKey(),
        ])->callAction('mark_delivered');

        $order->refresh();

        $this->assertSame(OrderStatus::DELIVERED, $order->status);
        $this->assertDatabaseHas('order_histories', [
            'order_id' => $order->id,
            'status' => OrderStatus::DELIVERED->value,
        ]);
    }

    public function test_admin_can_cancel_order_from_confirmation_page(): void
    {
        $this->actingAs($user = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($user);
        Filament::setCurrentPanel('admin');

        $order = Order::factory()->create([
            'status' => OrderStatus::PENDING,
            'payment_status' => PaymentStatus::PAID,
        ]);

        Livewire::test(CancelOrder::class, [
            'record' => $order->getRouteKey(),
        ])
            ->fillForm([
                'reason' => 'Customer changed mind',
            ])
            ->call('cancel');

        $order->refresh();

        $this->assertSame(OrderStatus::CANCELLED, $order->status);
        $this->assertSame(PaymentStatus::REFUND_PENDING, $order->payment_status);
        $this->assertDatabaseHas('order_histories', [
            'order_id' => $order->id,
            'status' => OrderStatus::CANCELLED->value,
        ]);
    }

    public function test_admin_can_cancel_paid_order_with_auto_refund(): void
    {
        $this->actingAs($user = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($user);
        Filament::setCurrentPanel('admin');

        $order = Order::factory()->create([
            'status' => OrderStatus::PROCESSING,
            'payment_status' => PaymentStatus::PAID,
        ]);

        Transaction::create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'transaction_ref' => 'pay-ref-123',
            'type' => TransactionType::Payment,
            'payment_method' => $order->payment_method,
            'amount' => $order->grand_total,
            'currency' => 'USD',
            'status' => TransactionStatus::Success,
            'gateway_response' => [],
            'description' => 'Payment success',
        ]);

        Livewire::test(CancelOrder::class, [
            'record' => $order->getRouteKey(),
        ])
            ->fillForm([
                'reason' => 'Admin cancelled',
                'refund_option' => CancelRefundOption::AUTO->value,
            ])
            ->call('cancel');

        $order->refresh();

        $this->assertSame(OrderStatus::CANCELLED, $order->status);
        $this->assertSame(PaymentStatus::REFUNDED, $order->payment_status);
        $this->assertDatabaseHas('transactions', [
            'order_id' => $order->id,
            'type' => TransactionType::Refund->value,
            'status' => TransactionStatus::Success->value,
        ]);
    }

    public function test_admin_can_process_manual_refund_after_cancellation(): void
    {
        $this->actingAs($user = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($user);
        Filament::setCurrentPanel('admin');

        $order = Order::factory()->create([
            'status' => OrderStatus::CANCELLED,
            'payment_status' => PaymentStatus::REFUND_PENDING,
        ]);

        Livewire::test(ManualRefundOrder::class, [
            'record' => $order->getRouteKey(),
        ])
            ->fillForm([
                'note' => 'Manual refund',
            ])
            ->call('processRefund');

        $order->refresh();

        $this->assertSame(PaymentStatus::REFUNDED, $order->payment_status);
        $this->assertDatabaseHas('transactions', [
            'order_id' => $order->id,
            'type' => TransactionType::Refund->value,
            'status' => TransactionStatus::Success->value,
        ]);
    }

    public function test_admin_can_create_manual_order_with_stock_movements(): void
    {
        $this->actingAs($admin = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($admin);
        Filament::setCurrentPanel('admin');

        $customer = User::factory()->create();
        $shippingAddress = UserAddress::create([
            'user_id' => $customer->id,
            'contact_person' => 'Ahmed',
            'contact_phone' => '0500000000',
            'address_line_1' => 'Street 1',
            'address_line_2' => null,
            'city' => 'Riyadh',
            'state' => 'Riyadh',
            'postal_code' => '12345',
            'country' => 'SA',
            'is_default_shipping' => true,
        ]);
        $variant = $this->createProductVariant(quantity: 5, price: '120.00');

        Livewire::test(CreateManualOrder::class)
            ->fillForm([
                'user_id' => $customer->id,
                'notes' => 'Phone order',
                'shipping_address_id' => $shippingAddress->id,
                'items' => [
                    [
                        'product_variant_id' => $variant->id,
                        'unit_price' => 120.0,
                        'quantity' => 2,
                    ],
                ],
                'discount_amount' => 0,
                'tax_amount' => 0,
                'shipping_cost' => 0,
                'transaction' => [
                    'transaction_ref' => 'MAN-REF-1',
                    'note' => 'Paid via bank transfer',
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $order = Order::query()->latest()->first();

        $this->assertNotNull($order);
        $this->assertSame($customer->id, $order->user_id);
        $this->assertSame(OrderStatus::PROCESSING, $order->status);
        $this->assertSame(PaymentStatus::PAID, $order->payment_status);
        $this->assertSame(PaymentMethod::BANK_TRANSFER, $order->payment_method);
        $this->assertSame($shippingAddress->contact_person, $order->shipping_address_snapshot['contact_person']);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_variant_id' => $variant->id,
            'quantity' => 2,
        ]);

        $this->assertDatabaseHas('transactions', [
            'order_id' => $order->id,
            'type' => TransactionType::Payment->value,
            'status' => TransactionStatus::Success->value,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'product_variant_id' => $variant->id,
            'type' => StockMovementType::SALE->value,
            'sourceable_type' => Order::class,
            'sourceable_id' => $order->id,
        ]);

        $variant->refresh();
        $this->assertSame(3, $variant->quantity);

        $this->assertDatabaseHas('order_histories', [
            'order_id' => $order->id,
            'status' => OrderStatus::PROCESSING->value,
        ]);
    }

    private function assignPanelRole(User $user): void
    {
        $role = Role::findOrCreate('panel_user');

        $user->assignRole($role);
    }

    private function createProductVariant(int $quantity, string $price): ProductVariant
    {
        $category = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics-'.Str::random(6),
            'description' => null,
            'image_path' => 'categories/electronics.png',
            'status' => \App\Enums\CategoryStatus::Published,
            'products_count' => 0,
            '_lft' => 1,
            '_rgt' => 2,
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'brand_id' => null,
            'name' => 'Test Product',
            'slug' => 'test-product-'.Str::random(6),
            'description' => null,
            'specifications' => null,
            'status' => \App\Enums\ProductStatus::Published,
            'featured' => false,
        ]);

        return ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'SKU-'.Str::random(8),
            'price' => $price,
            'quantity' => $quantity,
            'is_default' => true,
        ]);
    }
}
