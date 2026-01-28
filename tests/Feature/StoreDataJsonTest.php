<?php

namespace Tests\Feature;

use App\Enums\AttributeType;
use App\Enums\BrandStatus;
use App\Enums\CategoryStatus;
use App\Enums\ProductStatus;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Review;
use App\Models\User;
use App\Services\Seed\StoreDataExporter;
use App\Services\Seed\StoreDataImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreDataJsonTest extends TestCase
{
    use RefreshDatabase;

    public function test_exporter_returns_expected_payload(): void
    {
        $brand = Brand::create([
            'name' => 'علامة اختبار',
            'description' => 'وصف العلامة',
            'image_path' => 'brands/test.png',
            'status' => BrandStatus::Published,
            'featured' => true,
            'products_count' => 0,
        ]);

        $root = Category::create([
            'name' => 'جذر اختبار',
            'description' => 'وصف الجذر',
            'image_path' => 'categories/root.png',
            'status' => CategoryStatus::Published,
            'products_count' => 0,
        ]);

        $leaf = $root->children()->create([
            'name' => 'فرع اختبار',
            'description' => 'وصف الفرع',
            'image_path' => 'categories/leaf.png',
            'status' => CategoryStatus::Published,
            'products_count' => 0,
        ]);

        $product = Product::create([
            'category_id' => $leaf->id,
            'brand_id' => $brand->id,
            'name' => 'منتج اختبار',
            'description' => 'وصف المنتج',
            'status' => ProductStatus::Published,
            'featured' => false,
            'specifications' => ['المواصفة' => 'قيمة'],
        ]);

        $attribute = Attribute::create([
            'name' => 'اللون',
            'type' => AttributeType::Color,
        ]);

        $attributeValue = AttributeValue::create([
            'attribute_id' => $attribute->id,
            'value' => 'أسود',
            'color_code' => '#000000',
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'SKU-TEST-1',
            'price' => '100.00',
            'compare_at_price' => '120.00',
            'quantity' => 5,
            'is_default' => true,
        ]);

        $variant->attributeValues()->attach($attributeValue->id, ['attribute_id' => $attribute->id]);

        $variant->images()->create([
            'path' => 'seed-images/variants/test.png',
            'alt_text' => 'صورة اختبار',
            'display_order' => 0,
        ]);

        $user = User::create([
            'name' => 'مستخدم اختبار',
            'email' => 'store-data-test@example.com',
            'password' => 'Password123!',
            'is_active' => true,
            'is_admin' => false,
            'reset_password_required' => false,
        ]);

        Review::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rating' => 5,
            'comment' => 'مراجعة اختبار',
            'is_approved' => true,
        ]);

        $payload = app(StoreDataExporter::class)->export();

        $this->assertArrayHasKey('brands', $payload);
        $this->assertArrayHasKey('categories', $payload);
        $this->assertArrayHasKey('reviews', $payload);
        $this->assertCount(1, $payload['brands']);
        $this->assertCount(1, $payload['reviews']);
    }

    public function test_importer_creates_data_from_json(): void
    {
        $payload = [
            'brands' => [
                [
                    'name' => 'علامة مستوردة',
                    'description' => 'وصف العلامة',
                    'image_path' => 'brands/import.png',
                    'status' => BrandStatus::Published->value,
                    'featured' => false,
                ],
            ],
            'categories' => [
                [
                    'name' => 'جذر مستورد',
                    'description' => 'وصف الجذر',
                    'image_path' => 'categories/import-root.png',
                    'status' => CategoryStatus::Published->value,
                    'products_count' => 0,
                    'children' => [
                        [
                            'name' => 'فرع مستورد',
                            'description' => 'وصف الفرع',
                            'image_path' => 'categories/import-leaf.png',
                            'status' => CategoryStatus::Published->value,
                            'products_count' => 0,
                            'children' => [],
                            'products' => [
                                [
                                    'name' => 'منتج مستورد',
                                    'description' => 'وصف المنتج',
                                    'brand' => 'علامة مستوردة',
                                    'status' => ProductStatus::Published->value,
                                    'featured' => false,
                                    'specifications' => ['مواصفة' => 'قيمة'],
                                    'attributes' => [
                                        ['name' => 'الحجم', 'type' => AttributeType::Text->value],
                                    ],
                                    'variants' => [
                                        [
                                            'sku' => 'SKU-IMPORT-1',
                                            'price' => '150.00',
                                            'compare_at_price' => '180.00',
                                            'quantity' => 3,
                                            'is_default' => true,
                                            'attribute_values' => [
                                                'الحجم' => ['value' => 'متوسط'],
                                            ],
                                            'images' => [
                                                [
                                                    'path' => 'seed-images/variants/import.png',
                                                    'alt' => 'صورة مستوردة',
                                                    'display_order' => 0,
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'reviews' => [
                [
                    'rating' => 5,
                    'comment' => 'مراجعة مستوردة',
                    'is_approved' => true,
                    'user' => [
                        'name' => 'مستخدم مستورد',
                        'email' => 'import-user@example.com',
                        'gender' => 'male',
                        'is_active' => true,
                    ],
                    'product' => [
                        'name' => 'منتج مستورد',
                    ],
                ],
            ],
        ];

        $path = storage_path('app/store-data-import-test.json');
        file_put_contents($path, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        app(StoreDataImporter::class)->importFromPath($path);

        $this->assertDatabaseCount('brands', 1);
        $this->assertDatabaseCount('categories', 2);
        $this->assertDatabaseCount('products', 1);
        $this->assertDatabaseCount('product_variants', 1);
        $this->assertDatabaseCount('reviews', 1);
    }
}
