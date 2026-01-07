<?php

namespace Tests\Feature;

use Tests\TestCase;

class ImageUrlHelperTest extends TestCase
{
    public function test_storage_url_helper_is_present_and_used(): void
    {
        $utilsPath = base_path('resources/js/lib/utils.ts');

        $this->assertFileExists($utilsPath);
        $this->assertStringContainsString(
            'export function storageUrl',
            file_get_contents($utilsPath),
        );

        $files = [
            'resources/js/components/common/ProductCard/ProductCard.tsx',
            'resources/js/components/common/image-gallery.tsx',
            'resources/js/components/partials/Header/SearchBar/SearchBar.tsx',
            'resources/js/pages/store/cart/index.tsx',
            'resources/js/pages/store/checkout/index.tsx',
            'resources/js/pages/store/account/orders/show.tsx',
            'resources/js/features/account/orders/return-form.tsx',
            'resources/js/features/account/orders/order-card.tsx',
            'resources/js/features/account/returns/return-item-card.tsx',
            'resources/js/features/home/BrandCarousel/BrandCarousel.tsx',
            'resources/js/components/common/category-card.tsx',
        ];

        foreach ($files as $file) {
            $this->assertStringContainsString(
                'storageUrl(',
                file_get_contents(base_path($file)),
                sprintf('Expected storageUrl to be used in %s', $file),
            );
        }
    }
}
