<?php

namespace Tests\Feature;

use App\Enums\CategoryStatus;
use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Product;
use Database\Seeders\CustomerReviewsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerReviewsSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_reviews_seeder_creates_users_and_reviews(): void
    {
        $category = Category::create([
            'name' => 'فئة مراجعات',
            'description' => 'فئة للاختبار',
            'image_path' => 'categories/reviews-test.png',
            'status' => CategoryStatus::Published,
            'products_count' => 0,
        ]);

        $products = [
            'منتج اختبار 1',
            'منتج اختبار 2',
            'منتج اختبار 3',
        ];

        foreach ($products as $name) {
            Product::create([
                'category_id' => $category->id,
                'brand_id' => null,
                'name' => $name,
                'description' => 'وصف تجريبي',
                'status' => ProductStatus::Published,
                'featured' => false,
                'specifications' => null,
            ]);
        }

        (new CustomerReviewsSeeder)->run();

        $this->assertDatabaseCount('users', 20);
        $this->assertDatabaseCount('reviews', 20);
        $this->assertDatabaseHas('reviews', [
            'is_approved' => 1,
        ]);
    }
}
