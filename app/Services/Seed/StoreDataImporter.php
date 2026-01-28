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
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class StoreDataImporter
{
    /**
     * @var callable|null
     */
    private $progressReporter = null;

    /**
     * @var callable|null
     */
    private $errorReporter = null;

    public function importFromPath(string $path, ?callable $progressReporter = null, ?callable $errorReporter = null): void
    {
        $this->progressReporter = $progressReporter;
        $this->errorReporter = $errorReporter;

        if (! is_file($path)) {
            throw new RuntimeException('Import file not found: '.$path);
        }

        $payload = json_decode(file_get_contents($path), true);

        if (! is_array($payload)) {
            throw new RuntimeException('Invalid JSON payload.');
        }

        $brandsByName = $this->importBrands($payload['brands'] ?? []);

        foreach ($payload['categories'] ?? [] as $categoryData) {
            if (is_array($categoryData)) {
                $this->importCategoryTree($categoryData, null, $brandsByName);
            }
        }

        $this->importReviews($payload['reviews'] ?? []);
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
                $brand = Brand::query()->where('name', $name)->first();

                $attributes = [
                    'description' => Arr::get($brandData, 'description'),
                    'image_path' => Arr::get($brandData, 'image_path'),
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
            $status = $this->normalizeStatus($categoryData['status'] ?? CategoryStatus::Published->value, CategoryStatus::Published);

            $existing = Category::query()
                ->where('name', $name)
                ->when($parent, fn ($query) => $query->where('parent_id', $parent->id), fn ($query) => $query->whereNull('parent_id'))
                ->first();

            $attributes = [
                'name' => $name,
                'description' => $description,
                'image_path' => Arr::get($categoryData, 'image_path'),
                'status' => $status,
                'products_count' => $existing?->products_count ?? 0,
            ];

            if ($existing) {
                $existing->update($attributes);
                $category = $existing;
                $this->reportProgress('Category updated', ['name' => $name]);
            } else {
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
            }
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $images
     */
    private function importVariantImages(ProductVariant $variant, array $images): void
    {
        foreach ($images as $imageData) {
            if (! is_array($imageData)) {
                continue;
            }

            try {
                $path = $this->stringOrFail($imageData, 'path');
                $displayOrder = (int) ($imageData['display_order'] ?? 0);

                $variant->images()->updateOrCreate(
                    ['path' => $path],
                    [
                        'alt_text' => Arr::get($imageData, 'alt'),
                        'display_order' => $displayOrder,
                    ]
                );
            } catch (Throwable $exception) {
                $this->reportError('Variant image import failed', [
                    'sku' => $variant->sku,
                    'error' => $exception->getMessage(),
                ]);
            }
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $reviews
     */
    private function importReviews(array $reviews): void
    {
        foreach ($reviews as $reviewData) {
            if (! is_array($reviewData)) {
                continue;
            }

            try {
                $userData = Arr::get($reviewData, 'user', []);
                $productData = Arr::get($reviewData, 'product', []);

                $user = $this->resolveReviewUser($userData);
                $product = $this->resolveReviewProduct($productData);

                if (! $product) {
                    throw new RuntimeException('Review product not found.');
                }

                Review::query()->updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'product_id' => $product->id,
                    ],
                    [
                        'rating' => (int) ($reviewData['rating'] ?? 0),
                        'comment' => Arr::get($reviewData, 'comment'),
                        'is_approved' => (bool) ($reviewData['is_approved'] ?? true),
                    ],
                );

                $this->reportProgress('Review imported', [
                    'user' => $user->email,
                    'product' => $product->name,
                ]);
            } catch (Throwable $exception) {
                $this->reportError('Review import failed', [
                    'error' => $exception->getMessage(),
                    'comment' => $reviewData['comment'] ?? null,
                ]);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $userData
     */
    private function resolveReviewUser(array $userData): User
    {
        $email = $this->stringOrFail($userData, 'email');

        return User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => Arr::get($userData, 'name', $email),
                'gender' => Arr::get($userData, 'gender'),
                'password' => 'Password123!',
                'is_active' => (bool) ($userData['is_active'] ?? true),
                'is_admin' => false,
                'reset_password_required' => false,
            ],
        );
    }

    /**
     * @param  array<string, mixed>  $productData
     */
    private function resolveReviewProduct(array $productData): ?Product
    {
        $slug = $productData['slug'] ?? null;

        if (is_string($slug) && $slug !== '') {
            return Product::query()->where('slug', $slug)->first();
        }

        $name = $productData['name'] ?? null;

        if (is_string($name) && $name !== '') {
            return Product::query()->where('name', $name)->first();
        }

        return null;
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
