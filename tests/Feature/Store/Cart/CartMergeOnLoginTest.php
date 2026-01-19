<?php

namespace Tests\Feature\Store\Cart;

use App\Enums\BrandStatus;
use App\Enums\CategoryStatus;
use App\Enums\ProductStatus;
use App\Models\Brand;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Tests\TestCase;

class CartMergeOnLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_claims_session_cart_for_user(): void
    {
        Session::start();
        $sessionId = Session::getId();
        Session::put('pre_login_session_id', $sessionId);

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
            'name' => 'Test Product',
            'slug' => 'test-product-'.Str::random(6),
            'description' => null,
            'status' => ProductStatus::Published,
            'featured' => false,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'SKU-'.Str::random(8),
            'price' => '120.00',
            'quantity' => 10,
            'is_default' => true,
        ]);

        $sessionCart = Cart::create(['session_id' => $sessionId]);
        $sessionCart->items()->create([
            'product_variant_id' => $variant->id,
            'quantity' => 2,
            'is_selected' => true,
        ]);

        $userCart = Cart::create(['user_id' => $user->id]);
        $userCart->items()->create([
            'product_variant_id' => $variant->id,
            'quantity' => 1,
            'is_selected' => false,
        ]);

        Auth::login($user);

        $userCart->refresh();

        $this->assertDatabaseMissing('carts', [
            'id' => $sessionCart->id,
            'session_id' => $sessionId,
        ]);

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $userCart->id,
            'product_variant_id' => $variant->id,
            'quantity' => 3,
            'is_selected' => 1,
        ]);
    }
}
