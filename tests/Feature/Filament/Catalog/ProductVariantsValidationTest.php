<?php

namespace Tests\Feature\Filament\Catalog;

use App\Enums\AttributeType;
use App\Enums\CategoryStatus;
use App\Enums\ProductStatus;
use App\Filament\Resources\Catalog\Products\Pages\EditProduct;
use App\Filament\Resources\Catalog\Products\RelationManagers\VariantsRelationManager;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductVariantsValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_variant_attribute_name_cannot_change_on_edit(): void
    {
        $this->actingAs($user = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($user);
        Filament::setCurrentPanel('admin');

        [
            $product,
            $variant,
            $sizeAttribute,
            $colorAttribute,
            $sizeSmall,
            $colorRed,
            $sizeMedium,
            $colorBlue,
        ] = $this->createProductWithVariants();

        $variant->load('attributeValuesRaw');
        $variant->load('images');

        $sizeRow = $variant->attributeValuesRaw->firstWhere('attribute_id', $sizeAttribute->id);
        $colorRow = $variant->attributeValuesRaw->firstWhere('attribute_id', $colorAttribute->id);
        $image = $variant->images->first();

        Livewire::test(VariantsRelationManager::class, [
            'ownerRecord' => $product,
            'pageClass' => EditProduct::class,
        ])
            ->callAction(TestAction::make(EditAction::class)->table($variant), data: [
                'sku' => $variant->sku,
                'price' => $variant->price,
                'quantity' => $variant->quantity,
                'is_default' => $variant->is_default,
                'images' => [
                    'record-'.$image->id => [
                        'id' => $image->id,
                        'path' => [$image->path],
                        'alt_text' => $image->alt_text,
                        'display_order' => $image->display_order,
                    ],
                ],
                'attributeValues' => [
                    'record-'.$sizeRow->id => [
                        'id' => $sizeRow->id,
                        'attribute_id' => $colorAttribute->id,
                        'attribute_value_id' => $colorBlue->id,
                    ],
                    'record-'.$colorRow->id => [
                        'id' => $colorRow->id,
                        'attribute_id' => $sizeAttribute->id,
                        'attribute_value_id' => $sizeMedium->id,
                    ],
                ],
            ])
            ->assertHasErrors(['mountedActions.0.data.attributeValues.record-'.$sizeRow->id.'.attribute_id']);
    }

    public function test_variant_attribute_values_must_be_unique_per_product(): void
    {
        $this->actingAs($user = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($user);
        Filament::setCurrentPanel('admin');

        [
            $product,
            $variant,
            $sizeAttribute,
            $colorAttribute,
            $sizeSmall,
            $colorRed,
            $sizeMedium,
            $colorBlue,
        ] = $this->createProductWithVariants();

        $variant->load('attributeValuesRaw');
        $variant->load('images');

        $sizeRow = $variant->attributeValuesRaw->firstWhere('attribute_id', $sizeAttribute->id);
        $colorRow = $variant->attributeValuesRaw->firstWhere('attribute_id', $colorAttribute->id);
        $image = $variant->images->first();

        Livewire::test(VariantsRelationManager::class, [
            'ownerRecord' => $product,
            'pageClass' => EditProduct::class,
        ])
            ->callAction(TestAction::make(EditAction::class)->table($variant), data: [
                'sku' => $variant->sku,
                'price' => $variant->price,
                'quantity' => $variant->quantity,
                'is_default' => $variant->is_default,
                'images' => [
                    'record-'.$image->id => [
                        'id' => $image->id,
                        'path' => [$image->path],
                        'alt_text' => $image->alt_text,
                        'display_order' => $image->display_order,
                    ],
                ],
                'attributeValues' => [
                    'record-'.$sizeRow->id => [
                        'id' => $sizeRow->id,
                        'attribute_id' => $sizeAttribute->id,
                        'attribute_value_id' => $sizeSmall->id,
                    ],
                    'record-'.$colorRow->id => [
                        'id' => $colorRow->id,
                        'attribute_id' => $colorAttribute->id,
                        'attribute_value_id' => $colorRed->id,
                    ],
                ],
            ])
            ->assertHasErrors(['mountedActions.0.data.attributeValues.record-'.$sizeRow->id.'.attribute_value_id']);
    }

    public function test_variant_cannot_repeat_attribute_in_same_option(): void
    {
        Storage::fake('public');

        $this->actingAs($user = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($user);
        Filament::setCurrentPanel('admin');

        [
            $product,
            $variant,
            $sizeAttribute,
            $colorAttribute,
            $sizeSmall,
            $colorRed,
            $sizeMedium,
            $colorBlue,
        ] = $this->createProductWithVariants();

        $component = Livewire::test(VariantsRelationManager::class, [
            'ownerRecord' => $product,
            'pageClass' => EditProduct::class,
        ])
            ->callAction(TestAction::make(CreateAction::class)->table(), data: [
                'sku' => 'DUP-ATTR-'.Str::random(6),
                'price' => '199.00',
                'quantity' => 2,
                'is_default' => false,
                'images' => [
                    [
                        'path' => [
                            UploadedFile::fake()->createWithContent(
                                'variant.png',
                                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGNgYAAAAAMAASsJTYQAAAAASUVORK5CYII=')
                            ),
                        ],
                        'alt_text' => 'Variant',
                        'display_order' => 0,
                    ],
                ],
                'attributeValues' => [
                    [
                        'attribute_id' => $sizeAttribute->id,
                        'attribute_value_id' => $sizeSmall->id,
                    ],
                    [
                        'attribute_id' => $sizeAttribute->id,
                        'attribute_value_id' => $sizeMedium->id,
                    ],
                ],
            ]);
        $errorKeys = collect($component->errors()->keys());

        $this->assertTrue(
            $errorKeys->contains(fn (string $key): bool => str_contains($key, 'attributeValues') && str_ends_with($key, '.attribute_id'))
        );
    }

    private function assignPanelRole(User $user): void
    {
        $role = Role::findOrCreate('panel_user');

        $user->assignRole($role);
    }

    private function createProductWithVariants(): array
    {
        $category = Category::create([
            'name' => 'Validation Category',
            'slug' => 'validation-category-'.Str::random(6),
            'description' => null,
            'image_path' => 'categories/validation.png',
            'status' => CategoryStatus::Published,
            'products_count' => 0,
            '_lft' => 1,
            '_rgt' => 2,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'brand_id' => null,
            'name' => 'Validation Product',
            'slug' => 'validation-product-'.Str::random(6),
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

        Image::create([
            'path' => 'product-variants/default.jpg',
            'alt_text' => 'Default',
            'imageable_id' => $defaultVariant->id,
            'imageable_type' => ProductVariant::class,
            'display_order' => 0,
        ]);

        Image::create([
            'path' => 'product-variants/variant.jpg',
            'alt_text' => 'Variant',
            'imageable_id' => $variant->id,
            'imageable_type' => ProductVariant::class,
            'display_order' => 0,
        ]);

        return [$product, $variant, $sizeAttribute, $colorAttribute, $sizeSmall, $colorRed, $sizeMedium, $colorBlue];
    }
}
