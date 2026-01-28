<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Services\Seed\SeedDataImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SeedDataImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_imports_seed_data_from_json(): void
    {
        Storage::fake('public');
        Cache::flush();

        Http::fake(function ($request) {
            if (str_contains($request->url(), 'api.pexels.com/v1/search')) {
                return Http::response([
                    'photos' => [
                        [
                            'src' => [
                                'large2x' => 'https://images.pexels.com/photos/1/pexels-photo-1.jpeg',
                            ],
                        ],
                    ],
                ], 200);
            }

            return Http::response('image-bytes', 200, ['Content-Type' => 'image/jpeg']);
        });

        $importer = app(SeedDataImporter::class);
        $importer->importFromPath(base_path('tests/Fixtures/seed/mini-electronics.json'));

        $this->assertDatabaseCount('brands', 1);
        $this->assertDatabaseCount('categories', 3);
        $this->assertDatabaseCount('products', 1);
        $this->assertDatabaseCount('product_variants', 3);

        $category = Category::query()->where('name', 'الإلكترونيات المصغّرة')->first();
        $this->assertNotNull($category);

        $product = Product::query()->where('name', 'هاتف تجريبي برو')->first();
        $this->assertNotNull($product);

        $variants = $product->variants()->with('images', 'attributeValues')->get();
        $this->assertCount(3, $variants);

        foreach ($variants as $variant) {
            $this->assertCount(3, $variant->images);
        }
    }
}
