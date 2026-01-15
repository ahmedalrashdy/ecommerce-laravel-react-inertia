<?php

namespace App\Http\Controllers\Store\Home;

use App\Data\Basic\BrandData;
use App\Data\Basic\CategoryData;
use App\Data\Basic\ProductData;
use App\Data\Basic\ProductReviewData;
use App\Data\Basic\ProductReviewsSummaryData;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Services\Products\ProductDetailsService;
use App\Services\Search\SearchQueryService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class ProductController extends Controller
{
    public function index(
        Request $request,
        SearchQueryService $searchQueryService,
        ?Category $category = null,
        ?Brand $brand = null
    ): Response {
        $rawSearch = (string) $request->query('q', '');
        $search = $searchQueryService->normalize($rawSearch);

        $hasSearch = mb_strlen($search) >= 2;

        $query = Product::query()
            ->published()
            ->select('products.*')
            ->join('product_variants', function ($join) {
                $join->on('products.id', '=', 'product_variants.product_id')
                    ->where('product_variants.is_default', true);
            })
            ->with(['defaultVariant.defaultImage']);

        // Search (LIKE only) + tokens AND + ESCAPE
        $escapedSearch = null;
        $prefix = null;

        if ($hasSearch) {
            $escapedSearch = $searchQueryService->escapeLike($search);
            $prefix = "{$escapedSearch}%";

            $tokens = $searchQueryService->tokenize($search, 2, 5);

            foreach ($tokens as $token) {
                $esc = $searchQueryService->escapeLike($token);
                $like = "%{$esc}%";

                $query->where(function ($q) use ($like) {
                    $q->whereRaw("products.name LIKE ? ESCAPE '\\\\'", [$like]);
                });
            }
        }

        // filter by category,brand (مسار الصفحة)
        $query->when($category, fn($q) => $q->withinCategoryTree([$category->slug]))
            ->when($brand, fn($q) => $q->where('brand_id', $brand->id));
        $allowedSorts = [
            AllowedSort::callback('new-arrivals', fn($q) => $q->orderByDesc('products.created_at')),
            AllowedSort::callback('best-sellers', fn($q) => $q->bestSellers()),
            AllowedSort::callback('rating', fn($q) => $q->orderByDesc('products.rating_avg')),
            AllowedSort::callback('price-low', fn($q) => $q->orderBy('product_variants.price')),
            AllowedSort::callback('price-high', fn($q) => $q->orderByDesc('product_variants.price')),
            AllowedSort::callback('discount', function ($q) {
                $q->addSelect(\DB::raw('
                    COALESCE(
                        CASE
                            WHEN product_variants.compare_at_price IS NOT NULL
                             AND product_variants.compare_at_price > 0
                            THEN (product_variants.compare_at_price - product_variants.price) / product_variants.compare_at_price
                            ELSE 0
                        END
                    , 0) as discount_ratio
                '))
                    ->orderByDesc('discount_ratio');
            }),

        ];

        if ($hasSearch && $prefix !== null) {
            $allowedSorts[] = AllowedSort::callback('relevance', function ($q) use ($prefix) {
                $q->orderByRaw(
                    "CASE WHEN products.name LIKE ? ESCAPE '\\\\' THEN 0 ELSE 1 END",
                    [$prefix]
                )->orderBy('products.name');
            });
        }

        $sortParam = (string) $request->query('sort', '');

        if ($sortParam === '') {
            if ($hasSearch && $prefix !== null) {
                $query->orderByRaw(
                    "CASE WHEN products.name LIKE ? ESCAPE '\\\\' THEN 0 ELSE 1 END",
                    [$prefix]
                )->orderBy('products.name');
            } else {
                $query->orderByDesc('products.created_at');
            }
        }

        $productFilters = [
            AllowedFilter::callback('categories', fn($q, $value) => $q->withinCategoryTree($value)),
            AllowedFilter::callback('brands', function ($q, $value) {
                $slugs = is_array($value) ? $value : explode(',', $value);
                $q->joinRelationship('brand')
                    ->whereIn('brands.slug', $slugs);
            }),
            AllowedFilter::callback('min_price', fn($q, $value) => $q->where('product_variants.price', '>=', $value)),
            AllowedFilter::callback('max_price', fn($q, $value) => $q->where('product_variants.price', '<=', $value)),
        ];

        $products = QueryBuilder::for($query, $request)
            ->allowedFilters($productFilters)
            ->allowedSorts($allowedSorts)
            ->paginate(12);

        return Inertia::render('store/products/index', [
            'products' => ProductData::collect($products),
            'currentCategory' => $category ? CategoryData::from($category) : null,
            'currentBrand' => $brand ? BrandData::from($brand) : null,

            'filters' => $this->buildActiveFilters($request, $category, $brand, $hasSearch),
            'maxPrice' => $this->getMaxProductPrice(),
        ]);
    }

    private function getMaxProductPrice(): float
    {
        return \DB::table('product_variants')->max('price') ?? 5000;
    }

    private function buildActiveFilters(Request $request, ?Category $category, ?Brand $brand, bool $hasSearch): array
    {
        $filter = $request->query('filter', []);

        $sort = (string) $request->query('sort', '');
        if ($sort === '') {
            $sort = $hasSearch ? 'relevance' : 'new-arrivals';
        }

        return [
            'sort' => $sort,
            'minPrice' => $filter['min_price'] ?? null,
            'maxPrice' => $filter['max_price'] ?? null,
            'categories' => $this->parseSlugArrayParam($filter['categories'] ?? null),
            'brands' => $this->parseSlugArrayParam($filter['brands'] ?? null),
            'search' => $request->query('q'),
            'currentCategory' => $category?->slug,
            'currentBrand' => $brand?->slug,
        ];
    }

    private function parseSlugArrayParam(mixed $param): array
    {
        if (empty($param)) {
            return [];
        }
        $values = is_array($param) ? $param : explode(',', (string) $param);

        return array_values(array_filter($values, fn($v) => !empty($v)));
    }

    public function show(Request $request, Product $product): Response
    {
        return Inertia::render('store/products/product-details', [
            'product' => function () use ($product, $request) {
                $product->load([
                    'variants.images',
                    'variants.attributeValues.attribute',
                    'brand',
                    'category',
                ]);
                $filters = $request->input('filters', []);

                return app(ProductDetailsService::class)
                    ->getProductDetailsWithTargetVariant($product, $filters);
            },
            'reviewsSummary' => fn() => ProductReviewsSummaryData::fromProduct($product),
            'reviews' => Inertia::scroll(function () use ($product) {
                return $product->reviews()
                    ->where('is_approved', true)
                    ->select(['id', 'product_id', 'user_id', 'rating', 'comment', 'created_at'])
                    ->with(['user:id,name,avatar'])
                    ->latest()
                    ->paginate(4, pageName: 'reviews')
                    ->through(fn($review) => ProductReviewData::fromModel($review));
            }),
            'relatedProducts' => Inertia::defer(function () use ($product) {
                if (!$product->category) {
                    return [
                        'bestSellersInCategory' => [],
                        'topRatedInCategory' => [],
                    ];
                }

                $baseQuery = Product::published()
                    ->where('products.id', '!=', $product->id)
                    ->withinCategoryTree([$product->category->slug])
                    ->with(['defaultVariant.defaultImage']);

                $bestSellers = $baseQuery
                    ->bestSellers()
                    ->limit(20)
                    ->get();

                $topRated = $baseQuery
                    ->orderByDesc('rating_avg')
                    ->limit(20)
                    ->get();

                return [
                    'bestSellersInCategory' => ProductData::collect($bestSellers),
                    'topRatedInCategory' => ProductData::collect($topRated),
                ];
            })->once(),
        ]);
    }
}
