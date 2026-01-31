<?php

namespace App\Services\Seed;

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
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class SeedDataImporter
{
    /**
     * @var callable|null
     */
    private $progressReporter = null;

    /**
     * @var callable|null
     */
    private $errorReporter = null;

    public function __construct(private PexelsImageService $pexelsImageService) {}

    public function importFromPath(string $path, ?callable $progressReporter = null, ?callable $errorReporter = null): void
    {
        $this->progressReporter = $progressReporter;
        $this->errorReporter = $errorReporter;

        if (! is_file($path)) {
            throw new RuntimeException('Seed file not found: '.$path);
        }

        $payload = json_decode(file_get_contents($path), true);

        if (! is_array($payload)) {
            throw new RuntimeException('Invalid JSON payload.');
        }

        $brandsByName = $this->importBrands($payload['brands'] ?? []);
        $rootData = $payload['root_category'] ?? null;

        if (! is_array($rootData)) {
            throw new RuntimeException('Missing root_category in seed data.');
        }

        $rootCategory = $this->importCategoryTree($rootData, null, $brandsByName);

        if (! $rootCategory) {
            $this->reportError('Root category import failed.', ['name' => $rootData['name'] ?? null]);

            return;
        }

        $this->verifyImportedTree($rootData, $rootCategory);
    }

    /**
     * @param  array<int, array<string, mixed>>  $brands
     * @return array<string, Brand>
     */
    private function importBrands(array $brands): array
    {
        $map = [];

        foreach ($brands as $brandData) {
            try {
                $name = $this->stringOrFail($brandData, 'name');
                $imagePath = $this->resolveImagePath($brandData['image'] ?? null, 'seed-images/brands');

                $brand = Brand::query()->where('name', $name)->first();

                $attributes = [
                    'description' => Arr::get($brandData, 'description'),
                    'image_path' => $imagePath,
                    'status' => $this->normalizeStatus($brandData['status'] ?? BrandStatus::Published->value, BrandStatus::Published),
                    'featured' => (bool) ($brandData['featured'] ?? false),
                    'products_count' => $brand?->products_count ?? 0,
                ];

                if ($brand) {
                    $brand->update($attributes);
                    $this->reportProgress('Brand updated', ['name' => $name]);
                } else {
                    $brand = Brand::create(array_merge(['name' => $name], $attributes));
                    $this->reportProgress('Brand created', ['name' => $name]);
                }

                $map[$name] = $brand;
            } catch (Throwable $exception) {
                $this->reportError('Brand import failed', [
                    'name' => $brandData['name'] ?? null,
                    'error' => $exception->getMessage(),
                ]);

                continue;
            }
        }

        return $map;
    }

    /**
     * @param  array<string, mixed>  $categoryData
     * @param  array<string, Brand>  $brandsByName
     */
    private function importCategoryTree(array $categoryData, ?Category $parent, array $brandsByName): ?Category
    {
        try {
            $name = $this->stringOrFail($categoryData, 'name');
            $description = Arr::get($categoryData, 'description');
            $imagePath = $this->resolveImagePath($categoryData['image'] ?? null, 'seed-images/categories');
            $status = $this->normalizeStatus($categoryData['status'] ?? CategoryStatus::Published->value, CategoryStatus::Published);

            $existing = Category::query()
                ->where('name', $name)
                ->when($parent, fn ($query) => $query->where('parent_id', $parent->id), fn ($query) => $query->whereNull('parent_id'))
                ->first();

            if ($existing) {
                $existing->update([
                    'description' => $description,
                    'image_path' => $imagePath,
                    'status' => $status,
                ]);

                $category = $existing;
                $this->reportProgress('Category updated', ['name' => $name]);
            } else {
                $attributes = [
                    'name' => $name,
                    'description' => $description,
                    'image_path' => $imagePath,
                    'status' => $status,
                    'products_count' => 0,
                ];

                $category = $parent
                    ? $parent->children()->create($attributes)
                    : Category::create($attributes);

                $this->reportProgress('Category created', ['name' => $name]);
            }
        } catch (Throwable $exception) {
            $this->reportError('Category import failed', [
                'name' => $categoryData['name'] ?? null,
                'error' => $exception->getMessage(),
            ]);

            return null;
        }

        foreach ($categoryData['children'] ?? [] as $childData) {
            if (is_array($childData)) {
                $this->importCategoryTree($childData, $category, $brandsByName);
            }
        }

        foreach ($categoryData['products'] ?? [] as $productData) {
            if (is_array($productData)) {
                $this->importProduct($category, $productData, $brandsByName);
            }
        }

        return $category;
    }

    /**
     * @param  array<string, mixed>  $productData
     * @param  array<string, Brand>  $brandsByName
     */
    private function importProduct(Category $category, array $productData, array $brandsByName): ?Product
    {
        try {
            $name = $this->stringOrFail($productData, 'name');
            $brandName = $this->stringOrFail($productData, 'brand');
            $brand = $brandsByName[$brandName] ?? null;

            if (! $brand) {
                throw new RuntimeException('Unknown brand: '.$brandName);
            }

            $product = Product::query()
                ->where('name', $name)
                ->where('category_id', $category->id)
                ->first();

            $attributes = [
                'brand_id' => $brand->id,
                'description' => Arr::get($productData, 'description'),
                'status' => $this->normalizeStatus($productData['status'] ?? ProductStatus::Published->value, ProductStatus::Published),
                'featured' => (bool) ($productData['featured'] ?? false),
                'specifications' => Arr::get($productData, 'specifications'),
            ];

            if ($product) {
                $product->update($attributes);
                $this->reportProgress('Product updated', ['name' => $name]);
            } else {
                $product = $category->products()->create(array_merge(['name' => $name], $attributes));
                $this->reportProgress('Product created', ['name' => $name]);
            }
        } catch (Throwable $exception) {
            $this->reportError('Product import failed', [
                'name' => $productData['name'] ?? null,
                'error' => $exception->getMessage(),
            ]);

            return null;
        }

        $attributeMap = $this->importAttributes($productData['attributes'] ?? []);

        foreach ($productData['variants'] ?? [] as $variantData) {
            if (is_array($variantData)) {
                $this->importVariant($product, $variantData, $attributeMap);
            }
        }

        return $product;
    }

    /**
     * @param  array<int, array<string, mixed>>  $attributes
     * @return array<string, Attribute>
     */
    private function importAttributes(array $attributes): array
    {
        $map = [];

        foreach ($attributes as $attributeData) {
            $name = $this->stringOrFail($attributeData, 'name');
            $type = $this->normalizeAttributeType($attributeData['type'] ?? AttributeType::Text->value);

            $attribute = Attribute::query()->firstOrCreate(
                ['name' => $name],
                ['type' => $type],
            );

            $map[$name] = $attribute;
        }

        return $map;
    }

    /**
     * @param  array<string, Attribute>  $attributeMap
     * @param  array<string, mixed>  $variantData
     */
    private function importVariant(Product $product, array $variantData, array $attributeMap): ?ProductVariant
    {
        try {
            $sku = $this->stringOrFail($variantData, 'sku');

            $variant = ProductVariant::query()->where('sku', $sku)->first();

            $attributes = [
                'product_id' => $product->id,
                'price' => (string) ($variantData['price'] ?? '0'),
                'compare_at_price' => $variantData['compare_at_price'] ?? null,
                'quantity' => (int) ($variantData['quantity'] ?? 0),
                'is_default' => (bool) ($variantData['is_default'] ?? false),
            ];

            if ($variant) {
                $variant->update($attributes);
                $this->reportProgress('Variant updated', ['sku' => $sku]);
            } else {
                $variant = ProductVariant::create(array_merge(['sku' => $sku], $attributes));
                $this->reportProgress('Variant created', ['sku' => $sku]);
            }
        } catch (Throwable $exception) {
            $this->reportError('Variant import failed', [
                'sku' => $variantData['sku'] ?? null,
                'error' => $exception->getMessage(),
            ]);

            return null;
        }

        $this->syncVariantAttributes($variant, $variantData['attribute_values'] ?? [], $attributeMap);
        $this->importVariantImages($variant, $variantData['images'] ?? []);

        return $variant;
    }

    /**
     * @param  array<string, array<string, mixed>>  $attributeValues
     * @param  array<string, Attribute>  $attributeMap
     */
    private function syncVariantAttributes(ProductVariant $variant, array $attributeValues, array $attributeMap): void
    {
        foreach ($attributeValues as $attributeName => $valueData) {
            try {
                $attribute = $attributeMap[$attributeName] ?? null;

                if (! $attribute) {
                    throw new RuntimeException('Unknown attribute: '.$attributeName);
                }

                $value = is_array($valueData) ? ($valueData['value'] ?? null) : null;

                if (! is_string($value) || $value === '') {
                    throw new RuntimeException('Missing attribute value for: '.$attributeName);
                }

                $attributeValue = AttributeValue::query()->firstOrCreate(
                    ['attribute_id' => $attribute->id, 'value' => $value],
                    ['color_code' => Arr::get($valueData, 'color_code')],
                );

                $variant->attributeValues()->syncWithoutDetaching([
                    $attributeValue->id => ['attribute_id' => $attribute->id],
                ]);
            } catch (Throwable $exception) {
                $this->reportError('Variant attribute import failed', [
                    'sku' => $variant->sku,
                    'attribute' => $attributeName,
                    'error' => $exception->getMessage(),
                ]);

                continue;
            }
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $images
     */
    private function importVariantImages(ProductVariant $variant, array $images): void
    {
        foreach ($images as $index => $imageData) {
            if (! is_array($imageData)) {
                continue;
            }

            try {
                $path = $this->resolveImagePath($imageData, 'seed-images/variants');

                $variant->images()->updateOrCreate(
                    ['path' => $path],
                    [
                        'alt_text' => Arr::get($imageData, 'alt'),
                        'display_order' => $index,
                    ]
                );
            } catch (Throwable $exception) {
                $this->reportError('Variant image import failed', [
                    'sku' => $variant->sku,
                    'error' => $exception->getMessage(),
                ]);

                continue;
            }
        }
    }

    /**
     * @param  array<string, mixed>|null  $imageData
     */
    private function resolveImagePath(?array $imageData, string $folder): string
    {
        if (! $imageData) {
            throw new RuntimeException('Missing image data.');
        }

        $query = $this->stringOrFail($imageData, 'pexels_query');

        return $this->pexelsImageService->getImagePath($query, $folder);
    }

    private function normalizeAttributeType(mixed $value): int
    {
        if (is_string($value)) {
            $value = Str::lower($value);

            return match ($value) {
                'color', 'colour' => AttributeType::Color->value,
                default => AttributeType::Text->value,
            };
        }

        if (is_int($value)) {
            return $value;
        }

        return AttributeType::Text->value;
    }

    private function normalizeStatus(mixed $value, CategoryStatus|BrandStatus|ProductStatus $fallback): int
    {
        if (is_int($value)) {
            return $value;
        }

        return $fallback->value;
    }

    private function stringOrFail(array $data, string $key): string
    {
        $value = $data[$key] ?? null;

        if (! is_string($value) || $value === '') {
            throw new RuntimeException('Missing required field: '.$key);
        }

        return $value;
    }

    /**
     * @param  array<string, mixed>  $rootData
     */
    private function verifyImportedTree(array $rootData, Category $rootCategory): void
    {
        $expectedCategoryCount = 1 + $this->countNestedCategories($rootData['children'] ?? []);
        $actualCategoryCount = Category::descendantsAndSelf($rootCategory->id)->count();

        if ($expectedCategoryCount !== $actualCategoryCount) {
            $this->reportError('Category count mismatch', [
                'expected' => $expectedCategoryCount,
                'actual' => $actualCategoryCount,
            ]);
        }

        foreach ($rootData['children'] ?? [] as $levelOneData) {
            if (! is_array($levelOneData)) {
                continue;
            }

            $levelOne = Category::query()
                ->where('name', $this->stringOrFail($levelOneData, 'name'))
                ->where('parent_id', $rootCategory->id)
                ->first();

            if (! $levelOne) {
                $this->reportError('Missing level-1 category', ['name' => $levelOneData['name'] ?? null]);

                continue;
            }

            foreach ($levelOneData['children'] ?? [] as $levelTwoData) {
                if (! is_array($levelTwoData)) {
                    continue;
                }

                $levelTwo = Category::query()
                    ->where('name', $this->stringOrFail($levelTwoData, 'name'))
                    ->where('parent_id', $levelOne->id)
                    ->first();

                if (! $levelTwo) {
                    $this->reportError('Missing level-2 category', ['name' => $levelTwoData['name'] ?? null]);

                    continue;
                }

                $expectedProducts = count($levelTwoData['products'] ?? []);
                $actualProducts = $levelTwo->products()->count();

                if ($expectedProducts !== $actualProducts) {
                    $this->reportError('Product count mismatch', [
                        'category' => $levelTwo->name,
                        'expected' => $expectedProducts,
                        'actual' => $actualProducts,
                    ]);
                }

                foreach ($levelTwoData['products'] ?? [] as $productData) {
                    if (! is_array($productData)) {
                        continue;
                    }

                    $product = Product::query()
                        ->where('name', $this->stringOrFail($productData, 'name'))
                        ->where('category_id', $levelTwo->id)
                        ->first();

                    if (! $product) {
                        $this->reportError('Missing product', ['name' => $productData['name'] ?? null]);

                        continue;
                    }

                    $variants = $product->variants()->with('attributeValues')->get();

                    if ($variants->count() !== count($productData['variants'] ?? [])) {
                        $this->reportError('Variant count mismatch', [
                            'product' => $product->name,
                            'expected' => count($productData['variants'] ?? []),
                            'actual' => $variants->count(),
                        ]);

                        continue;
                    }

                    $this->verifyVariantAttributes($variants, $product->name);
                    $this->verifyVariantImages($variants, $product->name);
                }
            }
        }
    }

    /**
     * @param  Collection<int, ProductVariant>  $variants
     */
    private function verifyVariantAttributes(Collection $variants, string $productName): void
    {
        $attributeSets = $variants->map(function (ProductVariant $variant) {
            return $variant->attributeValues->pluck('attribute_id')->sort()->values()->implode(',');
        });

        if ($attributeSets->unique()->count() !== 1) {
            $this->reportError('Variant attributes mismatch', ['product' => $productName]);
        }

        $valueSets = $variants->map(function (ProductVariant $variant) {
            return $variant->attributeValues->pluck('id')->sort()->values()->implode(',');
        });

        if ($valueSets->unique()->count() <= 1) {
            $this->reportError('Variant attribute values not varied', ['product' => $productName]);
        }
    }

    /**
     * @param  Collection<int, ProductVariant>  $variants
     */
    private function verifyVariantImages(Collection $variants, string $productName): void
    {
        foreach ($variants as $variant) {
            $images = $variant->images()->get();

            if ($images->count() !== 3) {
                $this->reportError('Image count mismatch', [
                    'product' => $productName,
                    'sku' => $variant->sku,
                    'actual' => $images->count(),
                ]);
            }

            foreach ($images as $image) {
                if (Str::contains(Str::lower($image->path), 'placeholder')) {
                    $this->reportError('Placeholder image found', [
                        'product' => $productName,
                        'sku' => $variant->sku,
                    ]);
                }
            }
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $children
     */
    private function countNestedCategories(array $children): int
    {
        $count = 0;

        foreach ($children as $child) {
            if (! is_array($child)) {
                continue;
            }

            $count++;
            $count += $this->countNestedCategories($child['children'] ?? []);
        }

        return $count;
    }

    private function reportProgress(string $message, array $context = []): void
    {
        if (! $this->progressReporter) {
            return;
        }

        ($this->progressReporter)($message, $context);
    }

    private function reportError(string $message, array $context = []): void
    {
        if (! $this->errorReporter) {
            return;
        }

        ($this->errorReporter)($message, $context);
    }
}
