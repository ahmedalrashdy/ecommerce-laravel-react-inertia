<?php

namespace Tests\Feature\Store\Cart;

use App\Enums\AttributeType;
use App\Enums\BrandStatus;
use App\Enums\CategoryStatus;
use App\Enums\ProductStatus;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Brand;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CartAttributesTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_page_includes_variant_attributes(): void
    {
        $user = User::factory()->create();

        $category = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics-'.Str::random(6),
            'description' => null,
            'image_path' => 'categories/electronics.png',
            'status' => CategoryStatus::Published,
            'products_count' => 0,
            '_lft' => 1,
            '_rgt' => 2,
        ]);

        $brand = Brand::create([
            'name' => 'Acme',
            'slug' => 'acme-'.Str::random(6),
            'description' => null,
            'image_path' => 'brands/acme.png',
            'status' => BrandStatus::Published,
            'featured' => false,
            'products_count' => 0,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'name' => 'Attribute Product',
            'slug' => 'attribute-product-'.Str::random(6),
            'description' => null,
            'status' => ProductStatus::Published,
            'featured' => false,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'SKU-'.Str::random(8),
            'price' => '120.00',
            'quantity' => 3,
            'is_default' => true,
        ]);

        $colorAttribute = Attribute::create([
            'name' => 'Color',
            'type' => AttributeType::Color,
        ]);

        $colorValue = AttributeValue::create([
            'attribute_id' => $colorAttribute->id,
            'value' => 'Red',
            'color_code' => '#ff0000',
        ]);

        $variant->attributeValues()->attach($colorValue->id, [
            'attribute_id' => $colorAttribute->id,
        ]);

        $cart = Cart::create(['user_id' => $user->id]);
        $cart->items()->create([
            'product_variant_id' => $variant->id,
            'quantity' => 2,
            'is_selected' => true,
        ]);

        $response = $this->actingAs($user)->get(route('store.cart.index'));

        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('store/cart/index')
                ->has('cart.items', 1)
                ->has('cart.items.0.productVariant.attributes', 1)
                ->where('cart.items.0.productVariant.attributes.0.attributeName', 'Color')
                ->where('cart.items.0.productVariant.attributes.0.valueName', 'Red')
                ->where('cart.items.0.productVariant.attributes.0.colorCode', '#ff0000')
        );
    }
}
