<?php

namespace Tests\Feature\Store\Search;

use App\Enums\BrandStatus;
use App\Enums\CategoryStatus;
use App\Enums\ProductStatus;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SearchSuggestionsTest extends TestCase
{
    use RefreshDatabase;

    private function createCategory(string $name, int $status = CategoryStatus::Published->value): Category
    {
        return Category::create([
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(6),
            'description' => null,
            'image_path' => 'categories/test.png',
            'status' => $status,
            'products_count' => 0,
            '_lft' => 1,
            '_rgt' => 2,
        ]);
    }

    private function createBrand(string $name, int $status = BrandStatus::Published->value): Brand
    {
        return Brand::create([
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(6),
            'description' => null,
            'image_path' => 'brands/test.png',
            'status' => $status,
            'featured' => false,
            'products_count' => 0,
        ]);
    }

    private function createProduct(string $name, Category $category, ?Brand $brand = null, int $status = ProductStatus::Published->value): Product
    {
        $product = Product::create([
            'category_id' => $category->id,
            'brand_id' => $brand?->id,
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(6),
            'description' => 'Test product description',
            'specifications' => [],
            'status' => $status,
            'featured' => false,
        ]);

        ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'SKU-'.Str::random(8),
            'price' => '99.99',
            'quantity' => 10,
            'is_default' => true,
        ]);

        return $product;
    }

    public function test_returns_empty_suggestions_for_short_query(): void
    {
        $this->get(route('store.search.suggestions', ['q' => 'a']))
            ->assertOk()
            ->assertJson(['suggestions' => []]);
    }

    public function test_returns_empty_suggestions_for_empty_query(): void
    {
        $this->get(route('store.search.suggestions'))
            ->assertOk()
            ->assertJson(['suggestions' => []]);
    }

    public function test_returns_product_suggestions(): void
    {
        $category = $this->createCategory('Electronics');
        $product = $this->createProduct('iPhone 15 Pro Max', $category);

        $response = $this->get(route('store.search.suggestions', ['q' => 'iPhone']))
            ->assertOk();

        $suggestions = $response->json('suggestions');
        $this->assertNotEmpty($suggestions);
        $this->assertEquals('product', $suggestions[0]['type']);
        $this->assertStringContainsString('iPhone', $suggestions[0]['name']);
    }

    public function test_returns_category_suggestions(): void
    {
        $category = $this->createCategory('Electronics & Gadgets');

        $response = $this->get(route('store.search.suggestions', ['q' => 'Electronics']))
            ->assertOk();

        $suggestions = $response->json('suggestions');
        $categorySuggestions = array_filter($suggestions, fn ($s) => $s['type'] === 'category');

        $this->assertNotEmpty($categorySuggestions);
        $this->assertStringContainsString('Electronics', array_values($categorySuggestions)[0]['name']);
    }

    public function test_returns_brand_suggestions(): void
    {
        $brand = $this->createBrand('Samsung Electronics');

        $response = $this->get(route('store.search.suggestions', ['q' => 'Samsung']))
            ->assertOk();

        $suggestions = $response->json('suggestions');
        $brandSuggestions = array_filter($suggestions, fn ($s) => $s['type'] === 'brand');

        $this->assertNotEmpty($brandSuggestions);
        $this->assertStringContainsString('Samsung', array_values($brandSuggestions)[0]['name']);
    }

    public function test_does_not_return_draft_products(): void
    {
        $category = $this->createCategory('Electronics');
        $this->createProduct('Draft Product Test', $category, null, ProductStatus::Draft->value);

        $response = $this->get(route('store.search.suggestions', ['q' => 'Draft']))
            ->assertOk();

        $suggestions = $response->json('suggestions');
        $productSuggestions = array_filter($suggestions, fn ($s) => $s['type'] === 'product');

        $this->assertEmpty($productSuggestions);
    }

    public function test_does_not_return_draft_categories(): void
    {
        $this->createCategory('Draft Category Test', CategoryStatus::Draft->value);

        $response = $this->get(route('store.search.suggestions', ['q' => 'Draft']))
            ->assertOk();

        $suggestions = $response->json('suggestions');
        $categorySuggestions = array_filter($suggestions, fn ($s) => $s['type'] === 'category');

        $this->assertEmpty($categorySuggestions);
    }

    public function test_does_not_return_draft_brands(): void
    {
        $this->createBrand('Draft Brand Test', BrandStatus::Draft->value);

        $response = $this->get(route('store.search.suggestions', ['q' => 'Draft']))
            ->assertOk();

        $suggestions = $response->json('suggestions');
        $brandSuggestions = array_filter($suggestions, fn ($s) => $s['type'] === 'brand');

        $this->assertEmpty($brandSuggestions);
    }

    public function test_escapes_percent_wildcard_in_query(): void
    {
        $category = $this->createCategory('Electronics');
        $this->createProduct('Cotton Shirt 100', $category);
        $this->createProduct('Another Shirt', $category);

        // When searching for "%", it should not match everything (wildcard)
        // It should be treated as a literal character
        $response = $this->get(route('store.search.suggestions', ['q' => 'Shirt%']))
            ->assertOk();

        $suggestions = $response->json('suggestions');
        $productSuggestions = array_filter($suggestions, fn ($s) => $s['type'] === 'product');

        // Should not return results because no product has literal "Shirt%" in name
        $this->assertEmpty($productSuggestions);
    }

    public function test_escapes_underscore_wildcard_in_query(): void
    {
        $category = $this->createCategory('Electronics');
        $this->createProduct('Item Special Name', $category);
        $this->createProduct('ItemXSpecialXName', $category);

        // When searching for "_", it should not match any single character (wildcard)
        // It should be treated as a literal character
        $response = $this->get(route('store.search.suggestions', ['q' => 'Item_Special']))
            ->assertOk();

        $suggestions = $response->json('suggestions');
        $productSuggestions = array_filter($suggestions, fn ($s) => $s['type'] === 'product');

        // Should not return results because no product has literal "Item_Special" in name
        $this->assertEmpty($productSuggestions);
    }

    public function test_limits_product_suggestions(): void
    {
        $category = $this->createCategory('Electronics');

        for ($i = 1; $i <= 10; $i++) {
            $this->createProduct("Test Product {$i}", $category);
        }

        $response = $this->get(route('store.search.suggestions', ['q' => 'Test']))
            ->assertOk();

        $suggestions = $response->json('suggestions');
        $productSuggestions = array_filter($suggestions, fn ($s) => $s['type'] === 'product');

        $this->assertLessThanOrEqual(5, count($productSuggestions));
    }

    public function test_suggestion_includes_required_fields(): void
    {
        $category = $this->createCategory('Electronics');
        $brand = $this->createBrand('Apple');
        $this->createProduct('MacBook Pro', $category, $brand);

        $response = $this->get(route('store.search.suggestions', ['q' => 'MacBook']))
            ->assertOk();

        $suggestions = $response->json('suggestions');
        $productSuggestion = collect($suggestions)->firstWhere('type', 'product');

        $this->assertNotNull($productSuggestion);
        $this->assertArrayHasKey('id', $productSuggestion);
        $this->assertArrayHasKey('name', $productSuggestion);
        $this->assertArrayHasKey('slug', $productSuggestion);
        $this->assertArrayHasKey('type', $productSuggestion);
        $this->assertArrayHasKey('image', $productSuggestion);
        $this->assertArrayHasKey('price', $productSuggestion);
    }

    public function test_case_insensitive_search(): void
    {
        $category = $this->createCategory('Electronics');
        $this->createProduct('IPHONE PRO MAX', $category);

        $response = $this->get(route('store.search.suggestions', ['q' => 'iphone']))
            ->assertOk();

        $suggestions = $response->json('suggestions');
        $productSuggestions = array_filter($suggestions, fn ($s) => $s['type'] === 'product');

        $this->assertNotEmpty($productSuggestions);
    }

    public function test_arabic_text_search(): void
    {
        $category = $this->createCategory('ملابس');
        $this->createProduct('قميص رجالي أزرق', $category);

        $response = $this->get(route('store.search.suggestions', ['q' => 'قميص']))
            ->assertOk();

        $suggestions = $response->json('suggestions');

        $this->assertNotEmpty($suggestions);
    }
}
