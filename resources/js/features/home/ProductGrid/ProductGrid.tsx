import { ProductCard } from '@/components/common/ProductCard/ProductCard';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import products from '@/routes/store/products';
import { Link, usePage } from '@inertiajs/react';
import { ArrowLeft, ChevronLeft, ChevronRight } from 'lucide-react';
import * as React from 'react';
import { styles } from './ProductGrid.styles';

interface ProductGridProps {
    title: string;
    description?: string;
    products: App.Data.Basic.ProductData[];
    viewAllLink?: string;
    variant?: 'best-sellers' | 'new-arrivals';
    badge?: { text: string; color: string };
}

export const BestSellers: React.FC = () => {
    const {
        productsGrid: { bestSellers },
    } = usePage<{
        productsGrid: {
            bestSellers: App.Data.Basic.ProductData[];
        };
    }>().props;
    return (
        <ProductGrid
            title="الأكثر مبيعاً"
            description="المنتجات الأكثر طلباً من عملائنا"
            products={bestSellers}
            viewAllLink={products.index.url({
                query: { sort: 'best-sellers' },
            })}
            badge={{ text: 'الأكثر مبيعاً', color: 'bg-primary' }}
        />
    );
};

export const NewArrivals: React.FC = () => {
    const {
        productsGrid: { newArrivals },
    } = usePage<{
        productsGrid: {
            newArrivals: App.Data.Basic.ProductData[];
        };
    }>().props;
    return (
        <ProductGrid
            title="وصل حديثاً"
            description="أحدث المنتجات في متجرنا"
            products={newArrivals}
            viewAllLink={products.index.url({
                query: { sort: 'new-arrivals' },
            })}
            badge={{ text: 'وصل حديثاً', color: 'bg-indigo-500' }}
        />
    );
};

export const TopRated: React.FC = () => {
    const {
        productsGrid: { topRated },
    } = usePage<{
        productsGrid: {
            topRated: App.Data.Basic.ProductData[];
        };
    }>().props;
    return (
        <ProductGrid
            title="الأعلى تقييماً"
            description="المنتجات الأكثر تقييماً من عملائنا"
            products={topRated}
            viewAllLink={products.index.url({
                query: { sort: 'rating' },
            })}
            badge={{ text: 'الأعلى تقييماً', color: 'bg-amber-500' }}
        />
    );
};

export const ProductGrid: React.FC<ProductGridProps> = ({
    title,
    description,
    products,
    viewAllLink,
    badge,
}) => {
    const scrollContainerRef = React.useRef<HTMLDivElement>(null);
    const [canScrollRight, setCanScrollRight] = React.useState(false);
    const [canScrollLeft, setCanScrollLeft] = React.useState(true);

    // RTL-Safe Scroll Check Logic
    const checkScroll = () => {
        if (scrollContainerRef.current) {
            const container = scrollContainerRef.current;
            const scrollLeft = container.scrollLeft;
            const scrollWidth = container.scrollWidth;
            const clientWidth = container.clientWidth;
            const maxScroll = scrollWidth - clientWidth;
            const absoluteScrollLeft = Math.abs(scrollLeft);

            setCanScrollRight(absoluteScrollLeft > 5);
            setCanScrollLeft(absoluteScrollLeft < maxScroll - 5);
        }
    };

    // RTL-Safe Scroll Action
    const scroll = (direction: 'left' | 'right') => {
        if (scrollContainerRef.current) {
            const container = scrollContainerRef.current;
            // تقريباً عرض البطاقة + المسافة
            const scrollAmount = 300;

            // في RTL: اليسار هو التقدم (السالب) واليمين هو الرجوع (الموجب)
            const physicalDirection =
                direction === 'left' ? -scrollAmount : scrollAmount;

            container.scrollBy({
                left: physicalDirection,
                behavior: 'smooth',
            });
        }
    };

    React.useEffect(() => {
        const container = scrollContainerRef.current;
        if (container) {
            checkScroll();
            container.addEventListener('scroll', checkScroll);
            window.addEventListener('resize', checkScroll);
            return () => {
                container.removeEventListener('scroll', checkScroll);
                window.removeEventListener('resize', checkScroll);
            };
        }
    }, [products]);
    return (
        <section
            className={styles.section}
            dir="rtl"
        >
            <div className={styles.container}>
                {/* Section Header */}
                <div className={styles.header}>
                    <div className={styles.headerContent}>
                        <h2 className={styles.title}>{title}</h2>
                        {description && (
                            <p className={styles.description}>{description}</p>
                        )}
                    </div>

                    {/* Navigation & Link Group */}
                    <div className={styles.navGroup}>
                        {viewAllLink && (
                            <Link
                                href={viewAllLink}
                                className={styles.viewAllLink}
                            >
                                عرض الكل
                                <ArrowLeft className={styles.viewAllIcon} />
                            </Link>
                        )}

                        {/* Arrows - Desktop Only */}
                        <div className={styles.arrows}>
                            <Button
                                variant="outline"
                                size="icon"
                                className={styles.arrowButton}
                                onClick={() => scroll('right')}
                                disabled={!canScrollRight}
                                aria-label="التمرير لليمين"
                            >
                                <ChevronRight className={styles.arrowIcon} />
                            </Button>

                            <Button
                                variant="outline"
                                size="icon"
                                className={styles.arrowButton}
                                onClick={() => scroll('left')}
                                disabled={!canScrollLeft}
                                aria-label="التمرير لليسار"
                            >
                                <ChevronLeft className={styles.arrowIcon} />
                            </Button>
                        </div>
                    </div>
                </div>

                {/* Products Horizontal Scroll Container */}
                <div className={styles.sliderContainer}>
                    {/* Gradient Fade Effects (Desktop) */}
                    <div
                        className={cn(
                            styles.gradientRight,
                            canScrollRight
                                ? styles.gradientRightVisible
                                : styles.gradientRightHidden,
                        )}
                    />
                    <div
                        className={cn(
                            styles.gradientLeft,
                            canScrollLeft
                                ? styles.gradientLeftVisible
                                : styles.gradientLeftHidden,
                        )}
                    />

                    {/* Scrollable Area */}
                    <div
                        ref={scrollContainerRef}
                        className={styles.scrollContainer}
                        style={{
                            scrollbarWidth: 'none',
                            msOverflowStyle: 'none',
                        }}
                    >
                        {products.map((product) => (
                            <div
                                key={product.id}
                                className={styles.productWrapper}
                            >
                                <ProductCard
                                    product={product}
                                    badge={badge}
                                    viewMode="grid"
                                />
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </section>
    );
};
