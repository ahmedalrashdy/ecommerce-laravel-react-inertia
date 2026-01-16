<?php

namespace Tests\Feature\Filament\Catalog;

use App\Enums\CategoryStatus;
use App\Enums\ProductStatus;
use App\Filament\Resources\Catalog\Products\Pages\ViewProduct;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductSpecificationsViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_specifications_are_visible_on_view_page(): void
    {
        $this->actingAs($user = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($user);
        Filament::setCurrentPanel('admin');

        $product = $this->createProductWithSpecifications();

        Livewire::test(ViewProduct::class, [
            'record' => $product->slug,
        ])
            ->assertSee('Weight')
            ->assertSee('10kg')
            ->assertSee('Material')
            ->assertSee('Steel');
    }

    private function assignPanelRole(User $user): void
    {
        $role = Role::findOrCreate('panel_user');

        $user->assignRole($role);
    }

    private function createProductWithSpecifications(): Product
    {
        $category = Category::create([
            'name' => 'Specifications Category',
            'slug' => 'specifications-category-'.Str::random(6),
            'description' => null,
            'image_path' => 'categories/specifications.png',
            'status' => CategoryStatus::Published,
            'products_count' => 0,
            '_lft' => 1,
            '_rgt' => 2,
        ]);

        return Product::create([
            'category_id' => $category->id,
            'brand_id' => null,
            'name' => 'Specifications Product',
            'slug' => 'specifications-product-'.Str::random(6),
            'description' => null,
            'specifications' => [
                [
                    'key' => 'Weight',
                    'value' => '10kg',
                ],
                [
                    'key' => 'Material',
                    'value' => 'Steel',
                ],
            ],
            'status' => ProductStatus::Published,
            'featured' => false,
        ]);
    }
}
