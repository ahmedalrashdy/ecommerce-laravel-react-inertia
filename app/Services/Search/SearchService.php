<?php

namespace App\Services\Search;

use App\Enums\BrandStatus;
use App\Enums\CategoryStatus;
use App\Enums\ProductStatus;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SearchService
{
    private const MIN_QUERY_LENGTH = 2;

    private const MAX_PRODUCTS = 5;

    private const MAX_CATEGORIES = 3;

    private const MAX_BRANDS = 2;

    private const CACHE_TTL_SECONDS = 45;

    public function __construct(private SearchQueryService $searchQueryService) {}

    /**
     * Get search suggestions for autocomplete.
     *
     * @return Collection<int, array{
     *   id:int,
     *   name:string,
     *   slug:string,
     *   type:'product'|'category'|'brand',
     *   image:?string,
     *   price:?string
     * }>
     */
    public function getSuggestions(string $query): Collection
    {
        $query = $this->searchQueryService->normalize($query);

        if (mb_strlen($query) < self::MIN_QUERY_LENGTH) {
            return collect();
        }

        $cacheKey = 'search:suggest:'.sha1(mb_strtolower($query));

        return Cache::remember($cacheKey, self::CACHE_TTL_SECONDS, function () use ($query) {
            $tokens = $this->searchQueryService->tokenize($query, 1, 5);

            $products = $this->searchProducts($query, $tokens);
            $categories = $this->searchCategories($query, $tokens);
            $brands = $this->searchBrands($query, $tokens);

            return collect()
                ->merge($products)
                ->merge($categories)
                ->merge($brands);
        });
    }

    /**
     * @return Collection<int, array{ id:int,name:string,slug:string,type:'product',image:?string,price:?string }>
     */
    private function searchProducts(string $query, array $tokens): Collection
    {
        $escapedFull = $this->searchQueryService->escapeLike($query);
        $fullPrefix = $escapedFull.'%';

        $q = Product::query()
            ->where('status', ProductStatus::Published)
            ->select(['id', 'name', 'slug'])
            ->with([
                'defaultVariant:id,product_id,price',
                'defaultVariant.defaultImage:id,imageable_id,imageable_type,path',
            ]);

        // AND across tokens: كل كلمة لازم تظهر (ضمن name أو slug)
        foreach ($tokens as $token) {
            $escaped = $this->searchQueryService->escapeLike($token);
            $likeContains = '%'.$escaped.'%';

            $q->where(function ($sub) use ($likeContains) {
                $sub->whereRaw("name LIKE ? ESCAPE '\\\\'", [$likeContains])
                    ->orWhereRaw("slug LIKE ? ESCAPE '\\\\'", [$likeContains]);
            });
        }

        // ترتيب: prefix match على الاسم أولًا ثم slug ثم الباقي
        $q->orderByRaw(
            "CASE
                WHEN name LIKE ? ESCAPE '\\\\' THEN 0
                WHEN slug LIKE ? ESCAPE '\\\\' THEN 1
                ELSE 2
            END",
            [$fullPrefix, $fullPrefix]
        );

        // ثم ترتيب أبجدي بسيط لثبات النتائج
        $q->orderBy('name');

        return $q->limit(self::MAX_PRODUCTS)->get()->map(function (Product $product) {
            $imagePath = $product->defaultVariant?->defaultImage?->path;

            return [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'type' => 'product',
                'image' => $imagePath ? Storage::url($imagePath) : null,
                'price' => $product->defaultVariant?->price !== null
                    ? number_format((float) $product->defaultVariant->price, 2)
                    : null,
            ];
        });
    }

    /**
     * @return Collection<int, array{ id:int,name:string,slug:string,type:'category',image:?string,price:null }>
     */
    private function searchCategories(string $query, array $tokens): Collection
    {
        $escapedFull = $this->searchQueryService->escapeLike($query);
        $fullPrefix = $escapedFull.'%';

        $q = Category::query()
            ->where('status', CategoryStatus::Published)
            ->select(['id', 'name', 'slug', 'image_path']);

        foreach ($tokens as $token) {
            $escaped = $this->searchQueryService->escapeLike($token);
            $likeContains = '%'.$escaped.'%';

            $q->where(function ($sub) use ($likeContains) {
                $sub->whereRaw("name LIKE ? ESCAPE '\\\\'", [$likeContains])
                    ->orWhereRaw("slug LIKE ? ESCAPE '\\\\'", [$likeContains]);
            });
        }

        $q->orderByRaw(
            "CASE
                WHEN name LIKE ? ESCAPE '\\\\' THEN 0
                WHEN slug LIKE ? ESCAPE '\\\\' THEN 1
                ELSE 2
            END",
            [$fullPrefix, $fullPrefix]
        );

        $q->orderBy('name');

        return $q->limit(self::MAX_CATEGORIES)->get()->map(function (Category $category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'type' => 'category',
                'image' => $category->image_path ? Storage::url($category->image_path) : null,
                'price' => null,
            ];
        });
    }

    /**
     * @return Collection<int, array{ id:int,name:string,slug:string,type:'brand',image:?string,price:null }>
     */
    private function searchBrands(string $query, array $tokens): Collection
    {
        $escapedFull = $this->searchQueryService->escapeLike($query);
        $fullPrefix = $escapedFull.'%';

        $q = Brand::query()
            ->where('status', BrandStatus::Published)
            ->select(['id', 'name', 'slug', 'image_path']);

        foreach ($tokens as $token) {
            $escaped = $this->searchQueryService->escapeLike($token);
            $likeContains = '%'.$escaped.'%';

            $q->where(function ($sub) use ($likeContains) {
                $sub->whereRaw("name LIKE ? ESCAPE '\\\\'", [$likeContains])
                    ->orWhereRaw("slug LIKE ? ESCAPE '\\\\'", [$likeContains]);
            });
        }

        $q->orderByRaw(
            "CASE
                WHEN name LIKE ? ESCAPE '\\\\' THEN 0
                WHEN slug LIKE ? ESCAPE '\\\\' THEN 1
                ELSE 2
            END",
            [$fullPrefix, $fullPrefix]
        );

        $q->orderBy('name');

        return $q->limit(self::MAX_BRANDS)->get()->map(function (Brand $brand) {
            return [
                'id' => $brand->id,
                'name' => $brand->name,
                'slug' => $brand->slug,
                'type' => 'brand',
                'image' => $brand->image_path ? Storage::url($brand->image_path) : null,
                'price' => null,
            ];
        });
    }
}
