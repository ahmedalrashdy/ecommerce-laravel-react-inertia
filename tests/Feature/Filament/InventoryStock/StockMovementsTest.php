<?php

namespace Tests\Feature\Filament\InventoryStock;

use App\Enums\CategoryStatus;
use App\Enums\ProductStatus;
use App\Enums\StockMovementType;
use App\Filament\Resources\InventoryStock\StockMovements\Pages\ListStockMovements;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StockMovementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_stock_movements_list(): void
    {
        $this->actingAs($user = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($user);
        Filament::setCurrentPanel('admin');

        $variant = $this->createProductVariant(quantity: 5, price: '120.00');
        $movements = collect([
            StockMovement::create([
                'product_variant_id' => $variant->id,
                'type' => StockMovementType::SUPPLIER_RESTOCK,
                'quantity' => 3,
                'quantity_before' => 5,
                'quantity_after' => 8,
                'sourceable_type' => User::class,
                'sourceable_id' => $user->id,
                'description' => 'Initial shipment',
            ]),
            StockMovement::create([
                'product_variant_id' => $variant->id,
                'type' => StockMovementType::WASTE,
                'quantity' => -2,
                'quantity_before' => 8,
                'quantity_after' => 6,
                'sourceable_type' => User::class,
                'sourceable_id' => $user->id,
                'description' => 'Damaged items',
            ]),
        ]);

        Livewire::test(ListStockMovements::class)
            ->assertOk()
            ->assertCanSeeTableRecords($movements);
    }

    private function assignPanelRole(User $user): void
    {
        $role = Role::findOrCreate('panel_user');

        $user->assignRole($role);
    }

    private function createProductVariant(int $quantity, string $price): ProductVariant
    {
        $category = Category::create([
            'name' => 'Stock Category',
            'slug' => 'stock-category-'.Str::random(6),
            'description' => null,
            'image_path' => 'categories/stock.png',
            'status' => CategoryStatus::Published,
            'products_count' => 0,
            '_lft' => 1,
            '_rgt' => 2,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'brand_id' => null,
            'name' => 'Stock Product',
            'slug' => 'stock-product-'.Str::random(6),
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
