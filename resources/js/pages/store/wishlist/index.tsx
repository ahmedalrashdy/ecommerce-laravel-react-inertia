import { ProductCard } from '@/components/common/ProductCard/ProductCard';
import { Button } from '@/components/ui/button';
import StoreLayout from '@/layouts/StoreLayout';
import { Head, Link, usePage } from '@inertiajs/react';
import { Heart } from 'lucide-react';
import { styles } from './index.styles';

export default function WishlistPage() {
    const { products } = usePage<{
        products: App.Data.Basic.ProductData[];
    }>().props;
    return (
        <StoreLayout>
            <Head title="المفضلة" />
            <div className={styles.container}>
                <div className={styles.header}>
                    <h1 className={styles.title}>المفضلة</h1>
                    <p className={styles.subtitle}>
                        {products.length}{' '}
                        {products.length === 1 ? 'عنصر' : 'عناصر'}
                    </p>
                </div>

                {products.length === 0 ? (
                    <div className={styles.emptyContainer}>
                        <Heart className={styles.emptyIcon} />
                        <h2 className={styles.emptyTitle}>المفضلة فارغة</h2>
                        <p className={styles.emptyText}>
                            لم تقم بإضافة أي منتجات للمفضلة بعد
                        </p>
                        <Link href="/">
                            <Button className={styles.shopButton}>
                                ابدأ التسوق
                            </Button>
                        </Link>
                    </div>
                ) : (
                    <div className={styles.itemsGrid}>
                        {products.map((product) => {
                            return (
                                <ProductCard
                                    viewMode="grid"
                                    product={product}
                                />
                            );
                        })}
                    </div>
                )}
            </div>
        </StoreLayout>
    );
}
