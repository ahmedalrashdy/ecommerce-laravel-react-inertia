import { ProductGrid } from '@/features/home/ProductGrid/ProductGrid';
import { Deferred, usePage } from '@inertiajs/react';
import * as React from 'react';

interface RelatedProductsData {
    bestSellersInCategory: App.Data.Basic.ProductData[];
    topRatedInCategory: App.Data.Basic.ProductData[];
}

export const RelatedProductsSection: React.FC = () => {
    return (
        <Deferred
            data={'relatedProducts'}
            fallback={
                <div className="space-y-8">
                    <div className="h-64 animate-pulse rounded-lg bg-muted" />
                    <div className="h-64 animate-pulse rounded-lg bg-muted" />
                </div>
            }
        >
            <RelatedProducts />
        </Deferred>
    );
};
export default function RelatedProducts() {
    const {
        relatedProducts: { bestSellersInCategory, topRatedInCategory },
    } = usePage<{
        relatedProducts: RelatedProductsData;
    }>().props;
    return (
        <>
            {bestSellersInCategory.length > 0 && (
                <ProductGrid
                    title="الأكثر مبيعاً في نفس الفئة"
                    description="منتجات أخرى من نفس الفئة"
                    products={bestSellersInCategory}
                    badge={{
                        text: 'الأكثر مبيعاً',
                        color: 'bg-primary',
                    }}
                />
            )}
            {topRatedInCategory.length > 0 && (
                <ProductGrid
                    title="الأعلى تقييماً في نفس الفئة"
                    description="منتجات أخرى من نفس الفئة"
                    products={topRatedInCategory}
                    badge={{
                        text: 'الأعلى تقييماً',
                        color: 'bg-amber-500',
                    }}
                />
            )}
        </>
    );
}
