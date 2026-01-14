import { ProductCard } from '@/components/common/ProductCard/ProductCard';
import { cn } from '@/lib/utils';
import { PackageSearch } from 'lucide-react';

interface ProductsGridProps {
    products: App.Data.Basic.ProductData[];
    viewMode: 'grid' | 'list';
}

export function ProductsGrid({ products, viewMode }: ProductsGridProps) {
    if (products.length === 0) {
        return (
            <div className="flex flex-col items-center justify-center rounded-2xl border border-dashed border-border bg-card px-6 py-16 text-center">
                <div className="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-muted text-muted-foreground">
                    <PackageSearch className="h-6 w-6" />
                </div>
                <h3 className="text-lg font-semibold text-foreground">
                    لا توجد منتجات مطابقة
                </h3>
                <p className="mt-2 text-sm text-muted-foreground">
                    جرّب تعديل الفلاتر أو البحث عن كلمة مختلفة.
                </p>
            </div>
        );
    }

    return (
        <div
            className={cn(
                'gap-6',
                viewMode === 'grid'
                    ? 'grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4'
                    : 'flex flex-col gap-4',
            )}
        >
            {products.map((product) => (
                <ProductCard
                    key={product.id}
                    product={product}
                    viewMode={viewMode}
                />
            ))}
        </div>
    );
}
