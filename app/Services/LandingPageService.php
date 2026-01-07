<?php

namespace App\Services;

use App\Data\Basic\ProductData;
use App\Models\Product;

class LandingPageService
{
    public function __construct() {}

    public function getHeroSlides()
    {

        $maxDiscount = \Cache::remember('landing:hero_max_discount', now()->addHours(2), function () {
            return (int) \DB::table('product_variants')
                ->whereNotNull('compare_at_price')
                ->whereColumn('compare_at_price', '>', 'price')
                ->selectRaw('COALESCE(MAX(FLOOR((compare_at_price - price) / compare_at_price * 100)), 0) AS max_discount')
                ->value('max_discount');
        });

        $discountSubtitle = $maxDiscount > 0
            ? "خصومات حتى {$maxDiscount}%"
            : 'خصومات على منتجات مختارة';

        return [
            [
                'id' => 1,
                'title' => 'عروض وخصومات',
                'subtitle' => $discountSubtitle,
                'description' => 'خصومات رائعة.',
                'primaryCTA' => [
                    'text' => 'تسوق الخصومات',
                    'href' => '/products?sort=discount',
                ],
                'secondaryCTA' => [
                    'text' => 'جميع المنتجات',
                    'href' => '/products',
                ],
                'image' => 'https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=1920&q=80',
                'badge' => [
                    'text' => 'خصم مباشر',
                    'icon' => 'tag',
                ],
                'color' => 'from-rose-600 to-red-600',
            ],
            [
                'id' => 2,
                'title' => 'وصل حديثاً',
                'subtitle' => 'منتجات جديدة',
                'description' => 'اكتشف أحدث المنتجات المضافة إلى المتجر.',
                'primaryCTA' => [
                    'text' => 'استكشف الجديد',
                    'href' => '/products?sort=new-arrivals',
                ],
                'secondaryCTA' => [
                    'text' => 'تسوق الآن',
                    'href' => '/products',
                ],
                'image' => 'https://images.unsplash.com/photo-1550009158-9ebf69173e03?w=1920&q=80',
                'badge' => [
                    'text' => 'جديد',
                    'icon' => 'sparkles',
                ],
                'color' => 'from-violet-600 to-indigo-600',
            ],
            [
                'id' => 3,
                'title' => 'الأعلى تقييماً',
                'subtitle' => 'اختيارات العملاء',
                'description' => 'تسوق المنتجات الأعلى تقييماً بناءً على تقييمات العملاء.',
                'primaryCTA' => [
                    'text' => 'تسوق الأعلى تقييماً',
                    'href' => '/products?sort=rating',
                ],
                'secondaryCTA' => [
                    'text' => 'جميع المنتجات',
                    'href' => '/products',
                ],
                'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=1920&q=80',
                'badge' => [
                    'text' => 'Top Rated',
                    'icon' => 'star',
                ],
                'color' => 'from-sky-600 to-blue-700',
            ],
            [
                'id' => 4,
                'title' => 'الأكثر مبيعاً',
                'subtitle' => 'اختيارات العملاء',
                'description' => 'تسوق المنتجات الأعلى مبيعاً بناءً على بيانات المتجر.',
                'primaryCTA' => [
                    'text' => 'تسوق الأكثر مبيعاً',
                    'href' => '/products?sort=best-sellers',
                ],
                'secondaryCTA' => [
                    'text' => 'تسوق الخصومات',
                    'href' => '/products?sort=discount',
                ],
                'image' => 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=1920&q=80',
                'badge' => [
                    'text' => 'Top',
                    'icon' => 'star',
                ],
                'color' => 'from-amber-500 to-orange-600',
            ],
        ];

        // return [
        //     [
        //         'id' => 1,
        //         'title' => 'تخفيضات نهاية العام',
        //         'subtitle' => 'خصم يصل إلى 70%',
        //         'description' => 'اكتشف أفضل العروض على الإلكترونيات والأزياء والمزيد. الفرصة تأتي مرة واحدة في السنة.',
        //         'primaryCTA' => [
        //             'text' => 'تسوق العروض',
        //             'href' => '/shop',
        //         ],
        //         'secondaryCTA' => [
        //             'text' => 'تصفح الفئات',
        //             'href' => '/categories',
        //         ],
        //         'image' => 'https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=1920&q=80',
        //         'badge' => [
        //             'text' => 'عرض حصري',
        //             'icon' => 'flame',
        //         ],
        //         'color' => 'from-rose-600 to-red-600',
        //     ],
        //     [
        //         'id' => 2,
        //         'title' => 'عالم التقنية بين يديك',
        //         'subtitle' => 'وصل حديثاً 2024',
        //         'description' => 'تشكيلة جديدة من الأجهزة الذكية والإلكترونيات المتطورة التي تغير نمط حياتك.',
        //         'primaryCTA' => [
        //             'text' => 'استكشف الجديد',
        //             'href' => '/shop?filter=new',
        //         ],
        //         'image' => 'https://images.unsplash.com/photo-1550009158-9ebf69173e03?w=1920&q=80',
        //         'badge' => [
        //             'text' => 'الأكثر مبيعاً',
        //             'icon' => 'sparkles',
        //         ],
        //         'color' => 'from-violet-600 to-indigo-600',
        //     ],
        //     [
        //         'id' => 3,
        //         'title' => 'أناقة الشتاء الفاخرة',
        //         'subtitle' => 'موضة الموسم',
        //         'description' => 'أناقة لا مثيل لها مع تشكيلتنا الشتوية الجديدة. تصاميم عصرية بجودة عالمية.',
        //         'primaryCTA' => [
        //             'text' => 'تسوق الموضة',
        //             'href' => '/categories/fashion',
        //         ],
        //         'image' => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?w=1920&q=80',
        //         'badge' => [
        //             'text' => 'تشكيلة مميزة',
        //             'icon' => 'star',
        //         ],
        //         'color' => 'from-emerald-600 to-teal-600',
        //     ],
        //     [
        //         'id' => 4,
        //         'title' => 'عروض الـ Flash Sale',
        //         'subtitle' => 'لفترة محدودة جداً',
        //         'description' => 'لا تفوت الفرصة - خصومات حصرية تنتهي قريباً على منتجات مختارة.',
        //         'primaryCTA' => [
        //             'text' => 'اغتنم الفرصة',
        //             'href' => '/flash-deals',
        //         ],
        //         'secondaryCTA' => [
        //             'text' => 'جميع العروض',
        //             'href' => '/deals',
        //         ],
        //         'image' => 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=1920&q=80',
        //         'badge' => [
        //             'text' => 'عرض سريع',
        //             'icon' => 'zap',
        //         ],
        //         'color' => 'from-amber-500 to-orange-600',
        //     ],
        // ];

    }

    public function productsGrid()
    {
        $bestSellers = Product::published()->bestSellers()->limit(20)->get();
        $newArrivals = Product::published()->newArrivals()->limit(20)->get();
        $topRated = Product::published()->orderByDesc('rating_avg')->limit(20)->get();

        $allProducts = $bestSellers->merge($newArrivals)->merge($topRated);
        $allProducts->load(['defaultVariant.defaultImage']);

        $productsMap = $allProducts->keyBy('id');
        $bestSellersLoaded = $bestSellers->map(fn ($p) => $productsMap->get($p->id));
        $newArrivalsLoaded = $newArrivals->map(fn ($p) => $productsMap->get($p->id));
        $topRatedLoaded = $topRated->map(fn ($p) => $productsMap->get($p->id));

        return [
            'bestSellers' => ProductData::collect($bestSellersLoaded),
            'newArrivals' => ProductData::collect($newArrivalsLoaded),
            'topRated' => ProductData::collect($topRatedLoaded),
        ];
    }
}
