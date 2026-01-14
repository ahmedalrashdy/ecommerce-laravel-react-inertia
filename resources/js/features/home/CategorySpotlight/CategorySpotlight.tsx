import { CategoryCard } from '@/components/common/category-card';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import { usePage } from '@inertiajs/react';
import { ChevronLeft, ChevronRight } from 'lucide-react';
import * as React from 'react';
import styles from './CategorySpotlight.module.css';

export const CategorySpotlight: React.FC = () => {
    const { mainCategories: categories } = usePage<{
        mainCategories: App.Data.Basic.CategoryData[];
    }>().props;
    const scrollContainerRef = React.useRef<HTMLDivElement>(null);
    console.log(categories);
    // في RTL: نبدأ من اليمين، لذا لا يمكننا الذهاب يميناً (False) ولكن يمكننا الذهاب يساراً (True)
    const [canScrollRight, setCanScrollRight] = React.useState(false);
    const [canScrollLeft, setCanScrollLeft] = React.useState(true);

    const checkScroll = () => {
        if (scrollContainerRef.current) {
            const container = scrollContainerRef.current;
            const scrollLeft = container.scrollLeft;
            const scrollWidth = container.scrollWidth;
            const clientWidth = container.clientWidth;

            // حساب المسافة القصوى القابلة للتمرير
            const maxScroll = scrollWidth - clientWidth;

            // التعامل مع RTL في المتصفحات الحديثة (Chrome/Edge/Firefox)
            // في RTL: البداية (أقصى اليمين) تكون 0، والتمرير لليسار يعطي قيماً سالبة
            // ملاحظة: نستخدم Math.abs لتوحيد المنطق بغض النظر عن إشارة المتصفح
            const absoluteScrollLeft = Math.abs(scrollLeft);

            // نتحقق بوجود هامش خطأ بسيط (1px) لضمان الدقة
            // زر اليمين (Prev): يظهر إذا تحركنا قليلاً لليسار (أي ابتعدنا عن نقطة الصفر)
            setCanScrollRight(absoluteScrollLeft > 1);

            // زر اليسار (Next): يظهر إذا لم نصل للنهاية بعد
            setCanScrollLeft(absoluteScrollLeft < maxScroll - 1);
        }
    };

    const scroll = (direction: 'left' | 'right') => {
        if (scrollContainerRef.current) {
            const container = scrollContainerRef.current;
            const scrollAmount = 300; // مسافة التمرير

            // في RTL:
            // اتجاه "Left" (السهم الأيسر) يعني فيزيائياً تحريك المحتوى لليسار (قيمة سالبة)
            // اتجاه "Right" (السهم الأيمن) يعني فيزيائياً تحريك المحتوى لليمين (قيمة موجبة)
            // نستخدم scrollBy لأنه أسهل وأضمن من scrollTo في التعامل مع RTL

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
            // التحقق المبدئي
            checkScroll();

            // إضافة المستمعين
            container.addEventListener('scroll', checkScroll);
            window.addEventListener('resize', checkScroll);

            return () => {
                container.removeEventListener('scroll', checkScroll);
                window.removeEventListener('resize', checkScroll);
            };
        }
    }, []);

    return (
        <section
            className={styles.section}
            dir="rtl"
        >
            <div className={styles.container}>
                {/* Section Header */}
                <div className={styles.header}>
                    <div className={styles.headerContent}>
                        <h2 className={styles.title}>تسوق حسب الفئة</h2>
                        <p className={styles.description}>
                            اكتشف مجموعتنا الواسعة من الفئات المتنوعة
                        </p>
                    </div>

                    {/* Navigation Buttons - Desktop Only */}
                    {/* في RTL: السهم الأيمن (للرجوع) والسهم الأيسر (للمزيد) */}
                    <div className={styles.navButtons}>
                        <Button
                            variant="outline"
                            size="icon"
                            className={styles.navButton}
                            onClick={() => scroll('right')}
                            disabled={!canScrollRight}
                            aria-label="التمرير لليمين"
                        >
                            <ChevronRight className={styles.navButtonIcon} />
                        </Button>

                        <Button
                            variant="outline"
                            size="icon"
                            className={styles.navButton}
                            onClick={() => scroll('left')}
                            disabled={!canScrollLeft}
                            aria-label="التمرير لليسار"
                        >
                            <ChevronLeft className={styles.navButtonIcon} />
                        </Button>
                    </div>
                </div>

                {/* Horizontal Scroll Container */}
                <div className={styles.sliderContainer}>
                    {/* Gradient Fade Effects */}
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

                    <div
                        ref={scrollContainerRef}
                        className={styles.scrollContainer}
                        style={{
                            scrollbarWidth: 'none',
                            msOverflowStyle: 'none',
                        }} // Ensure scrollbar is hidden
                    >
                        {categories.map((category) => (
                            <CategoryCard
                                key={category.id}
                                category={category}
                            />
                        ))}
                    </div>
                </div>
            </div>
        </section>
    );
};
