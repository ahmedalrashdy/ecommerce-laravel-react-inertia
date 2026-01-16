<?php

namespace Tests\Feature\Filament\Catalog;

use App\Enums\AttributeType;
use App\Enums\CategoryStatus;
use App\Enums\ProductStatus;
use App\Filament\Resources\Catalog\Products\Pages\AddProductAttribute;
use App\Filament\Resources\Catalog\Products\Pages\ViewProduct;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductAttributeManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_add_attribute_to_all_variants(): void
    {
        $this->actingAs($user = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($user);
        Filament::setCurrentPanel('admin');

        [
            $product,
            $sizeAttribute,
            $colorAttribute,
            $sizeSmall,
            $colorRed,
            $sizeMedium,
            $colorBlue,
        ] = $this->createProductWithVariants();

        $materialAttribute = Attribute::create([
            'name' => 'Material',
            'type' => AttributeType::Text,
        ]);

        $cotton = AttributeValue::create([
            'attribute_id' => $materialAttribute->id,
            'value' => 'Cotton',
            'color_code' => null,
        ]);

        Livewire::test(AddProductAttribute::class, [
            'record' => $product->slug,
        ])
            ->fillForm([
                'attribute_id' => $materialAttribute->id,
                'variants' => [
                    [
                        'variant_id' => $product->variants->first()->id,
                        'attribute_value_id' => $cotton->id,
                    ],
                    [
                        'variant_id' => $product->variants->last()->id,
                        'attribute_value_id' => $cotton->id,
                    ],
                ],
            ])
            ->call('save');

        $variantIds = $product->variants()->pluck('id');

        $this->assertDatabaseHas('attribute_value_product_variant', [
            'product_variant_id' => $variantIds->first(),
            'attribute_id' => $materialAttribute->id,
            'attribute_value_id' => $cotton->id,
        ]);

        $this->assertDatabaseHas('attribute_value_product_variant', [
            'product_variant_id' => $variantIds->last(),
            'attribute_id' => $materialAttribute->id,
            'attribute_value_id' => $cotton->id,
        ]);
    }

    public function test_admin_can_delete_attribute_from_all_variants(): void
    {
        $this->actingAs($user = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($user);
        Filament::setCurrentPanel('admin');

        [
            $product,
            $sizeAttribute,
            $colorAttribute,
            $sizeSmall,
            $colorRed,
            $sizeMedium,
            $colorBlue,
        ] = $this->createProductWithVariants();

        Livewire::test(ViewProduct::class, [
            'record' => $product->slug,
        ])
            ->callAction('deleteAttribute', data: [
                'attribute_id' => $sizeAttribute->id,
            ]);

        $this->assertDatabaseMissing('attribute_value_product_variant', [
            'attribute_id' => $sizeAttribute->id,
        ]);
    }

    private function assignPanelRole(User $user): void
    {
        $role = Role::findOrCreate('panel_user');

        $user->assignRole($role);
    }

    private function createProductWithVariants(): array
    {
        $category = Category::create([
            'name' => 'Attribute Category',
            'slug' => 'attribute-category-'.Str::random(6),
            'description' => null,
            'image_path' => 'categories/attributes.png',
            'status' => CategoryStatus::Published,
            'products_count' => 0,
            '_lft' => 1,
            '_rgt' => 2,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'brand_id' => null,
            'name' => 'Attribute Product',
            'slug' => 'attribute-product-'.Str::random(6),
            'description' => null,
            'specifications' => null,
            'status' => ProductStatus::Published,
            'featured' => false,
        ]);

        $sizeAttribute = Attribute::create([
            'name' => 'Size',
            'type' => AttributeType::Text,
        ]);

        $colorAttribute = Attribute::create([
            'name' => 'Color',
            'type' => AttributeType::Text,
        ]);

        $sizeSmall = AttributeValue::create([
            'attribute_id' => $sizeAttribute->id,
            'value' => 'S',
            'color_code' => null,
        ]);

        $sizeMedium = AttributeValue::create([
            'attribute_id' => $sizeAttribute->id,
            'value' => 'M',
            'color_code' => null,
        ]);

        $colorRed = AttributeValue::create([
            'attribute_id' => $colorAttribute->id,
            'value' => 'Red',
            'color_code' => '#ff0000',
        ]);

        $colorBlue = AttributeValue::create([
            'attribute_id' => $colorAttribute->id,
            'value' => 'Blue',
            'color_code' => '#0000ff',
        ]);

        $defaultVariant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'SKU-'.Str::random(8),
            'price' => '120.00',
            'quantity' => 5,
            'is_default' => true,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'SKU-'.Str::random(8),
            'price' => '150.00',
            'quantity' => 8,
            'is_default' => false,
        ]);

        $defaultVariant->attributeValues()->attach($sizeSmall->id, ['attribute_id' => $sizeAttribute->id]);
        $defaultVariant->attributeValues()->attach($colorRed->id, ['attribute_id' => $colorAttribute->id]);

        $variant->attributeValues()->attach($sizeMedium->id, ['attribute_id' => $sizeAttribute->id]);
        $variant->attributeValues()->attach($colorBlue->id, ['attribute_id' => $colorAttribute->id]);

        return [
            $product->refresh(),
            $sizeAttribute,
            $colorAttribute,
            $sizeSmall,
            $colorRed,
            $sizeMedium,
            $colorBlue,
        ];
    }
}
