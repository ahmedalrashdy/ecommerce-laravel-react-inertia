<?php

namespace Tests\Feature\Filament\Returns;

use App\Enums\ItemCondition;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\ReturnResolution;
use App\Enums\ReturnStatus;
use App\Enums\StockMovementType;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Filament\Resources\OrdersManagement\Returns\Pages\CreateManualReturn;
use App\Filament\Resources\OrdersManagement\Returns\Pages\InspectReturnOrder;
use App\Filament\Resources\OrdersManagement\Returns\Pages\ListRequestedReturns;
use App\Filament\Resources\OrdersManagement\Returns\Pages\ListReturnOrders;
use App\Filament\Resources\OrdersManagement\Returns\Pages\ViewReturnOrder;
use App\Filament\Resources\OrdersManagement\Returns\RelationManagers\StockMovementsRelationManager;
use App\Filament\Resources\OrdersManagement\Returns\RelationManagers\TransactionsRelationManager;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ReturnItem;
use App\Models\ReturnOrder;
use App\Models\StockMovement;
use App\Models\Transaction;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReturnOrderResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_returns_list(): void
    {
        $this->actingAs($user = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($user);
        Filament::setCurrentPanel('admin');

        $returns = ReturnOrder::factory()->count(2)->create();

        Livewire::test(ListReturnOrders::class)
            ->assertCanSeeTableRecords($returns);
    }

    public function test_admin_can_view_requested_returns_list(): void
    {
        $this->actingAs($user = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($user);
        Filament::setCurrentPanel('admin');

        $requested = ReturnOrder::factory()->create([
            'status' => ReturnStatus::REQUESTED,
        ]);

        $approved = ReturnOrder::factory()->create([
            'status' => ReturnStatus::APPROVED,
        ]);

        Livewire::test(ListRequestedReturns::class)
            ->assertCanSeeTableRecords([$requested])
            ->assertCanNotSeeTableRecords([$approved]);
    }

    public function test_admin_can_create_manual_return_for_delivered_order(): void
    {
        $this->actingAs($user = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($user);
        Filament::setCurrentPanel('admin');

        $order = Order::factory()->create(['status' => OrderStatus::DELIVERED]);
        $variant = $this->createProductVariant(quantity: 3, price: '120.00');

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_variant_id' => $variant->id,
            'product_id' => $variant->product_id,
            'product_variant_snapshot' => OrderItem::createVariantSnapshot($variant),
            'price' => '120.00',
            'quantity' => 2,
            'discount_amount' => '0.00',
        ]);

        Livewire::test(CreateManualReturn::class)
            ->fillForm([
                'order_id' => $order->id,
                'return_scope' => 'partial',
                'status' => ReturnStatus::APPROVED->value,
                'reason' => 'Damaged',
                'items' => [
                    [
                        'order_item_id' => $orderItem->id,
                        'quantity' => 1,
                        'reason' => 'Damaged',
                    ],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $returnOrder = ReturnOrder::query()->latest()->first();

        $this->assertNotNull($returnOrder);
        $this->assertSame($order->id, $returnOrder->order_id);
        $this->assertSame(ReturnStatus::APPROVED, $returnOrder->status);
        $this->assertNull($returnOrder->tracking_number);
        $this->assertDatabaseHas('return_histories', [
            'return_id' => $returnOrder->id,
            'status' => ReturnStatus::APPROVED->value,
        ]);
    }

    public function test_manual_return_items_cannot_exceed_order_item_quantity(): void
    {
        $this->actingAs($user = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($user);
        Filament::setCurrentPanel('admin');

        $order = Order::factory()->create(['status' => OrderStatus::DELIVERED]);
        $variant = $this->createProductVariant(quantity: 10, price: '120.00');

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_variant_id' => $variant->id,
            'product_id' => $variant->product_id,
            'product_variant_snapshot' => OrderItem::createVariantSnapshot($variant),
            'price' => '120.00',
            'quantity' => 3,
            'discount_amount' => '0.00',
        ]);

        Livewire::test(CreateManualReturn::class)
            ->fillForm([
                'order_id' => $order->id,
                'return_scope' => 'partial',
                'status' => ReturnStatus::APPROVED->value,
                'reason' => 'Damaged',
                'items' => [
                    [
                        'order_item_id' => $orderItem->id,
                        'quantity' => 2,
                        'reason' => 'Damaged',
                    ],
                    [
                        'order_item_id' => $orderItem->id,
                        'quantity' => 2,
                        'reason' => 'Damaged',
                    ],
                ],
            ])
            ->call('create')
            ->assertHasFormErrors(['items']);
    }

    public function test_manual_return_not_allowed_for_reshipment_orders(): void
    {
        $this->actingAs($user = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($user);
        Filament::setCurrentPanel('admin');

        $order = Order::factory()->create([
            'status' => OrderStatus::DELIVERED,
            'type' => OrderType::RETURN_SHIPMENT,
        ]);

        Livewire::test(CreateManualReturn::class)
            ->fillForm([
                'order_id' => $order->id,
                'return_scope' => 'full',
                'status' => ReturnStatus::REQUESTED->value,
                'reason' => 'Customer request',
            ])
            ->call('create')
            ->assertHasFormErrors(['order_id']);
    }

    public function test_admin_can_create_full_return_with_all_order_items(): void
    {
        $this->actingAs($user = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($user);
        Filament::setCurrentPanel('admin');

        $order = Order::factory()->create(['status' => OrderStatus::DELIVERED]);
        $variant = $this->createProductVariant(quantity: 5, price: '200.00');

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_variant_id' => $variant->id,
            'product_id' => $variant->product_id,
            'product_variant_snapshot' => OrderItem::createVariantSnapshot($variant),
            'price' => '200.00',
            'quantity' => 2,
            'discount_amount' => '0.00',
        ]);

        Livewire::test(CreateManualReturn::class)
            ->fillForm([
                'order_id' => $order->id,
                'return_scope' => 'full',
                'status' => ReturnStatus::REQUESTED->value,
                'reason' => 'Customer request',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $returnOrder = ReturnOrder::query()->latest()->first();

        $this->assertNotNull($returnOrder);
        $this->assertSame($order->id, $returnOrder->order_id);
        $this->assertSame(ReturnStatus::REQUESTED, $returnOrder->status);
        $this->assertCount(1, $returnOrder->items);
        $this->assertDatabaseHas('return_items', [
            'return_id' => $returnOrder->id,
            'order_item_id' => $orderItem->id,
            'quantity' => 2,
            'reason' => 'Customer request',
        ]);
    }

    public function test_admin_can_approve_return_request(): void
    {
        $this->actingAs($user = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($user);
        Filament::setCurrentPanel('admin');

        $returnOrder = ReturnOrder::factory()->create([
            'status' => ReturnStatus::REQUESTED,
        ]);

        Livewire::test(ViewReturnOrder::class, [
            'record' => $returnOrder->getRouteKey(),
        ])->callAction('approve_return');

        $returnOrder->refresh();

        $this->assertSame(ReturnStatus::APPROVED, $returnOrder->status);
        $this->assertDatabaseHas('return_histories', [
            'return_id' => $returnOrder->id,
            'status' => ReturnStatus::APPROVED->value,
        ]);
    }

    public function test_admin_can_mark_return_as_shipped_back_with_tracking(): void
    {
        $this->actingAs($user = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($user);
        Filament::setCurrentPanel('admin');

        $returnOrder = ReturnOrder::factory()->create([
            'status' => ReturnStatus::APPROVED,
        ]);

        Livewire::test(ViewReturnOrder::class, [
            'record' => $returnOrder->getRouteKey(),
        ])->callAction('mark_shipped_back', [
            'tracking_number' => 'RET-TRACK-123',
        ]);

        $returnOrder->refresh();

        $this->assertSame(ReturnStatus::SHIPPED_BACK, $returnOrder->status);
        $this->assertSame('RET-TRACK-123', $returnOrder->tracking_number);
        $this->assertDatabaseHas('return_histories', [
            'return_id' => $returnOrder->id,
            'status' => ReturnStatus::SHIPPED_BACK->value,
        ]);
    }

    public function test_admin_can_reject_return_request(): void
    {
        $this->actingAs($user = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($user);
        Filament::setCurrentPanel('admin');

        $returnOrder = ReturnOrder::factory()->create([
            'status' => ReturnStatus::REQUESTED,
        ]);

        Livewire::test(ViewReturnOrder::class, [
            'record' => $returnOrder->getRouteKey(),
        ])->callAction('reject_return', [
            'reason' => 'Rejected by admin',
        ]);

        $returnOrder->refresh();

        $this->assertSame(ReturnStatus::REJECTED, $returnOrder->status);
        $this->assertSame('Rejected by admin', $returnOrder->admin_notes);
        $this->assertDatabaseHas('return_histories', [
            'return_id' => $returnOrder->id,
            'status' => ReturnStatus::REJECTED->value,
        ]);
    }

    public function test_admin_can_view_return_stock_movements(): void
    {
        $this->actingAs($user = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($user);
        Filament::setCurrentPanel('admin');

        $variant = $this->createProductVariant(quantity: 3, price: '120.00');
        $returnOrder = ReturnOrder::factory()->create();

        $movement = StockMovement::create([
            'product_variant_id' => $variant->id,
            'type' => StockMovementType::RETURN_RESTOCK,
            'quantity' => 2,
            'quantity_before' => 1,
            'quantity_after' => 3,
            'sourceable_type' => ReturnOrder::class,
            'sourceable_id' => $returnOrder->id,
            'description' => 'Return restock',
        ]);

        Livewire::test(StockMovementsRelationManager::class, [
            'ownerRecord' => $returnOrder,
            'pageClass' => ViewReturnOrder::class,
        ])->assertCanSeeTableRecords([$movement]);
    }

    public function test_admin_can_view_return_transactions(): void
    {
        $this->actingAs($user = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($user);
        Filament::setCurrentPanel('admin');

        $returnOrder = ReturnOrder::factory()->create();

        $transaction = Transaction::create([
            'order_id' => $returnOrder->order_id,
            'user_id' => $returnOrder->user_id,
            'transaction_ref' => 'REF-123',
            'type' => TransactionType::Refund,
            'payment_method' => PaymentMethod::CREDIT_CARD,
            'amount' => '25.00',
            'currency' => 'USD',
            'status' => TransactionStatus::Success,
            'gateway_response' => [],
            'description' => 'Return refund',
        ]);

        Livewire::test(TransactionsRelationManager::class, [
            'ownerRecord' => $returnOrder,
            'pageClass' => ViewReturnOrder::class,
        ])->assertCanSeeTableRecords([$transaction]);
    }

    public function test_admin_can_complete_return_inspection_workflow(): void
    {
        $this->actingAs($user = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($user);
        Filament::setCurrentPanel('admin');

        $order = Order::factory()->create([
            'status' => OrderStatus::DELIVERED,
        ]);
        $variant = $this->createProductVariant(quantity: 5, price: '120.00');

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_variant_id' => $variant->id,
            'product_id' => $variant->product_id,
            'product_variant_snapshot' => OrderItem::createVariantSnapshot($variant),
            'price' => '120.00',
            'quantity' => 2,
            'discount_amount' => '0.00',
        ]);

        $returnOrder = ReturnOrder::factory()->create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'status' => ReturnStatus::RECEIVED,
        ]);

        $returnItem = ReturnItem::create([
            'return_id' => $returnOrder->id,
            'order_item_id' => $orderItem->id,
            'quantity' => 2,
            'reason' => 'Damaged',
        ]);

        Livewire::test(InspectReturnOrder::class, [
            'record' => $returnOrder->getRouteKey(),
        ])->fillForm([
            'transaction_reference' => 'REF-123',
        ])->call('save')
            ->assertHasNoFormErrors();

        $returnOrder->refresh();
        $order->refresh();

        $this->assertSame(ReturnStatus::COMPLETED, $returnOrder->status);
        $this->assertSame(OrderStatus::RETURNED, $order->status);
        $this->assertSame(PaymentStatus::REFUNDED, $order->payment_status);
        $this->assertDatabaseHas('return_item_inspections', [
            'return_item_id' => $returnItem->id,
            'condition' => ItemCondition::SEALED->value,
            'resolution' => ReturnResolution::REFUND->value,
            'quantity' => 2,
        ]);
        $this->assertDatabaseHas('transactions', [
            'order_id' => $order->id,
            'type' => TransactionType::Refund->value,
            'transaction_ref' => 'REF-123',
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
