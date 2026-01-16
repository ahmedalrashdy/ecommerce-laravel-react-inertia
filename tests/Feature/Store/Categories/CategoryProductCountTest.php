<?php

namespace Tests\Feature\Store\Categories;

use App\Enums\CategoryStatus;
use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CategoryProductCountTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_counts_increment_for_category_ancestors(): void
    {
        $root = Category::create([
            'name' => 'Clothing',
            'slug' => 'clothing-'.Str::random(6),
            'description' => null,
            'image_path' => null,
            'status' => CategoryStatus::Published,
        ]);

        $parent = Category::make([
            'name' => 'Women',
            'slug' => 'women-'.Str::random(6),
            'description' => null,
            'image_path' => null,
            'status' => CategoryStatus::Published,
        ]);
        $parent->appendToNode($root)->save();
        $parent->refresh();

        $child = Category::make([
            'name' => 'Women Underwear',
            'slug' => 'women-underwear-'.Str::random(6),
            'description' => null,
            'image_path' => null,
            'status' => CategoryStatus::Published,
        ]);
        $child->appendToNode($parent)->save();
        $child->refresh();

        Product::create([
            'category_id' => $child->id,
            'brand_id' => null,
            'name' => 'Basic Tee',
            'slug' => 'basic-tee-'.Str::random(6),
            'description' => null,
            'status' => ProductStatus::Published,
            'featured' => false,
        ]);

        $this->assertSame(1, $child->fresh()->products_count);
        $this->assertSame(1, $parent->fresh()->products_count);
        $this->assertSame(1, $root->fresh()->products_count);
    }

    public function test_product_counts_move_between_category_branches(): void
    {
        $root = Category::create([
            'name' => 'Clothing',
            'slug' => 'clothing-'.Str::random(6),
            'description' => null,
            'image_path' => null,
            'status' => CategoryStatus::Published,
        ]);

        $women = Category::make([
            'name' => 'Women',
            'slug' => 'women-'.Str::random(6),
            'description' => null,
            'image_path' => null,
            'status' => CategoryStatus::Published,
        ]);
        $women->appendToNode($root)->save();
        $women->refresh();

        $men = Category::make([
            'name' => 'Men',
            'slug' => 'men-'.Str::random(6),
            'description' => null,
            'image_path' => null,
            'status' => CategoryStatus::Published,
        ]);
        $men->appendToNode($root)->save();
        $men->refresh();

        $womenChild = Category::make([
            'name' => 'Women Underwear',
            'slug' => 'women-underwear-'.Str::random(6),
            'description' => null,
            'image_path' => null,
            'status' => CategoryStatus::Published,
        ]);
        $womenChild->appendToNode($women)->save();
        $womenChild->refresh();

        $menChild = Category::make([
            'name' => 'Men Underwear',
            'slug' => 'men-underwear-'.Str::random(6),
            'description' => null,
            'image_path' => null,
            'status' => CategoryStatus::Published,
        ]);
        $menChild->appendToNode($men)->save();
        $menChild->refresh();

        $product = Product::create([
            'category_id' => $womenChild->id,
            'brand_id' => null,
            'name' => 'Basic Tee',
            'slug' => 'basic-tee-'.Str::random(6),
            'description' => null,
            'status' => ProductStatus::Published,
            'featured' => false,
        ]);

        $this->assertSame(1, $womenChild->fresh()->products_count);
        $this->assertSame(1, $women->fresh()->products_count);
        $this->assertSame(1, $root->fresh()->products_count);

        $product->update(['category_id' => $menChild->id]);

        $this->assertSame(0, $womenChild->fresh()->products_count);
        $this->assertSame(0, $women->fresh()->products_count);
        $this->assertSame(1, $menChild->fresh()->products_count);
        $this->assertSame(1, $men->fresh()->products_count);
        $this->assertSame(1, $root->fresh()->products_count);
    }
}
