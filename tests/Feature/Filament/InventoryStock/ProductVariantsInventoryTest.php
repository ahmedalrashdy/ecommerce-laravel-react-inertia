<?php

namespace Tests\Feature\Filament\InventoryStock;

use App\Enums\CategoryStatus;
use App\Enums\ProductStatus;
use App\Enums\StockMovementType;
use App\Filament\Resources\InventoryStock\ProductVariants\Pages\ListProductVariants;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductVariantsInventoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_variants_inventory(): void
    {
        $this->actingAs($user = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($user);
        Filament::setCurrentPanel('admin');

        $variants = collect([
            $this->createProductVariant(quantity: 5, price: '120.00'),
            $this->createProductVariant(quantity: 8, price: '180.00'),
        ]);

        Livewire::test(ListProductVariants::class)
            ->assertOk()
            ->assertCanSeeTableRecords($variants);
    }

    public function test_admin_can_restock_variant_inventory(): void
    {
        $this->actingAs($user = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($user);
        Filament::setCurrentPanel('admin');

        $variant = $this->createProductVariant(quantity: 5, price: '120.00');

        Livewire::test(ListProductVariants::class)
            ->callAction(
                TestAction::make('supplier_restock')->table($variant),
                data: [
                    'quantity' => 3,
                    'description' => 'New shipment',
                ]
            );

        $variant->refresh();
        $this->assertSame(8, $variant->quantity);

        $this->assertDatabaseHas('stock_movements', [
            'product_variant_id' => $variant->id,
            'type' => StockMovementType::SUPPLIER_RESTOCK->value,
            'quantity' => 3,
            'sourceable_type' => User::class,
            'sourceable_id' => $user->id,
        ]);
    }

    public function test_admin_can_adjust_variant_inventory(): void
    {
        $this->actingAs($user = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($user);
        Filament::setCurrentPanel('admin');

        $variant = $this->createProductVariant(quantity: 5, price: '120.00');

        Livewire::test(ListProductVariants::class)
            ->callAction(
                TestAction::make('adjustment')->table($variant),
                data: [
                    'new_quantity' => 10,
                    'description' => 'Cycle count',
                ]
            );

        $variant->refresh();
        $this->assertSame(10, $variant->quantity);

        $this->assertDatabaseHas('stock_movements', [
            'product_variant_id' => $variant->id,
            'type' => StockMovementType::ADJUSTMENT->value,
            'quantity' => 5,
            'sourceable_type' => User::class,
            'sourceable_id' => $user->id,
        ]);
    }

    public function test_admin_can_mark_variant_as_waste(): void
    {
        $this->actingAs($user = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($user);
        Filament::setCurrentPanel('admin');

        $variant = $this->createProductVariant(quantity: 5, price: '120.00');

        Livewire::test(ListProductVariants::class)
            ->callAction(
                TestAction::make('waste')->table($variant),
                data: [
                    'quantity' => 2,
                    'description' => 'Damaged items',
                ]
            );

        $variant->refresh();
        $this->assertSame(3, $variant->quantity);

        $this->assertDatabaseHas('stock_movements', [
            'product_variant_id' => $variant->id,
            'type' => StockMovementType::WASTE->value,
            'quantity' => -2,
            'sourceable_type' => User::class,
            'sourceable_id' => $user->id,
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
            'name' => 'Inventory Category',
            'slug' => 'inventory-category-'.Str::random(6),
            'description' => null,
            'image_path' => 'categories/inventory.png',
            'status' => CategoryStatus::Published,
            'products_count' => 0,
            '_lft' => 1,
            '_rgt' => 2,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'brand_id' => null,
            'name' => 'Inventory Product',
            'slug' => 'inventory-product-'.Str::random(6),
            'description' => null,
            'specifications' => null,
            'status' => ProductStatus::Published,
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
