import { cn } from '@/lib/utils';
import { show } from '@/routes/store/category';
import { Link, usePage } from '@inertiajs/react';
import { ChevronDown, ChevronLeft, Grid3x3 } from 'lucide-react';
import * as React from 'react';
import { styles } from './MegaNavigation.styles';

interface MegaNavigationProps {
    categories: App.Data.Basic.CategoryData[];
}

export const MegaNavigation: React.FC<MegaNavigationProps> = ({
    categories,
}) => {
    const { currentCategory } = usePage<{
        currentCategory?: App.Data.Basic.CategoryData | null;
    }>().props;

    const [activeCategory, setActiveCategory] = React.useState<number | null>(
        null,
    );
    const [activeSubCategory, setActiveSubCategory] = React.useState<
        number | null
    >(null);
    const timeoutRef = React.useRef<NodeJS.Timeout | null>(null);

    const isCategoryActive = (category: App.Data.Basic.CategoryData): boolean => {
        if (!currentCategory) {
            return false;
        }

        if (category.slug === currentCategory.slug) {
            return true;
        }

        const findParentPath = (
            tree: App.Data.Basic.CategoryData[],
            targetSlug: string,
            path: string[] = [],
        ): string[] | null => {
            for (const cat of tree) {
                const currentPath = [...path, cat.slug];
                if (cat.slug === targetSlug) {
                    return currentPath;
                }
                if (cat.children && cat.children.length > 0) {
                    const found = findParentPath(cat.children, targetSlug, currentPath);
                    if (found) {
                        return found;
                    }
                }
            }
            return null;
        };

        const parentPath = findParentPath(categories, currentCategory.slug);
        return parentPath ? parentPath.includes(category.slug) : false;
    };

    const handleMouseEnter = (categoryId: number) => {
        if (timeoutRef.current) {
            clearTimeout(timeoutRef.current);
        }
        setActiveCategory(categoryId);
        setActiveSubCategory(null);
    };

    const handleMouseLeave = () => {
        timeoutRef.current = setTimeout(() => {
            setActiveCategory(null);
            setActiveSubCategory(null);
        }, 200);
    };

    const activeMenuData = categories.find((cat) => cat.id === activeCategory);
    const activeSubMenuData = activeMenuData?.children?.find(
        (sub) => sub.id === activeSubCategory,
    );

    return (
        <div className={styles.wrapper}>
            <div className={styles.container}>
                <nav
                    className={styles.nav}
                    onMouseLeave={handleMouseLeave}
                >
                    {categories.slice(0, 8).map((category) => (
                        <div
                            key={category.id}
                            className={styles.categoryItem}
                        >
                            <Link
                                href={show(category.slug).url}
                                onMouseEnter={() =>
                                    handleMouseEnter(category.id)
                                }
                                className={cn(
                                    styles.categoryLink,
                                    (activeCategory === category.id ||
                                        isCategoryActive(category)) &&
                                        styles.categoryLinkActive,
                                )}
                            >
                                {category.name}
                                {category.children &&
                                    category.children.length > 0 && (
                                        <ChevronDown
                                            className={cn(
                                                styles.categoryChevron,
                                                (activeCategory === category.id ||
                                                    isCategoryActive(category)) &&
                                                    styles.categoryChevronActive,
                                            )}
                                        />
                                    )}
                                {(activeCategory === category.id ||
                                    isCategoryActive(category)) && (
                                    <span
                                        className={styles.categoryIndicator}
                                    />
                                )}
                            </Link>
                        </div>
                    ))}
                </nav>
            </div>

            {activeCategory &&
                activeMenuData &&
                activeMenuData.children &&
                activeMenuData.children.length > 0 && (
                    <div
                        className={styles.megaPanel}
                        onMouseEnter={() => {
                            if (timeoutRef.current) {
                                clearTimeout(timeoutRef.current);
                            }
                        }}
                        onMouseLeave={handleMouseLeave}
                    >
                        <div className={styles.megaPanelContainer}>
                            <div className={styles.megaPanelGrid}>
                                <div className={styles.subCategoriesColumn}>
                                    <div className={styles.subCategoriesHeader}>
                                        <div
                                            className={
                                                styles.subCategoriesIndicator
                                            }
                                        />
                                        <h3
                                            className={
                                                styles.subCategoriesTitle
                                            }
                                        >
                                            {activeMenuData.name}
                                        </h3>
                                    </div>
                                    <ul className={styles.subCategoriesList}>
                                        {activeMenuData.children.map(
                                            (subCategory) => (
                                                <li key={subCategory.id}>
                                                    <Link
                                                        href={
                                                            show(
                                                                subCategory.slug,
                                                            ).url
                                                        }
                                                        onMouseEnter={() =>
                                                            setActiveSubCategory(
                                                                subCategory.id,
                                                            )
                                                        }
                                                        className={cn(
                                                            styles.subCategoryLink,
                                                            activeSubCategory ===
                                                                subCategory.id &&
                                                                styles.subCategoryLinkActive,
                                                        )}
                                                    >
                                                        <span>
                                                            {subCategory.name}
                                                        </span>
                                                        {subCategory.children &&
                                                            subCategory.children
                                                                .length > 0 && (
                                                                <ChevronLeft
                                                                    className={cn(
                                                                        styles.subCategoryChevron,
                                                                        activeSubCategory ===
                                                                            subCategory.id &&
                                                                            styles.subCategoryChevronActive,
                                                                    )}
                                                                />
                                                            )}
                                                    </Link>
                                                </li>
                                            ),
                                        )}
                                    </ul>
                                </div>

                                <div className={styles.itemsColumn}>
                                    {activeSubCategory &&
                                    activeSubMenuData &&
                                    activeSubMenuData.children ? (
                                        <div>
                                            <div className={styles.itemsHeader}>
                                                <div
                                                    className={
                                                        styles.itemsIndicator
                                                    }
                                                />
                                                <h4
                                                    className={
                                                        styles.itemsTitle
                                                    }
                                                >
                                                    {activeSubMenuData.name}
                                                </h4>
                                                <span
                                                    className={
                                                        styles.itemsCount
                                                    }
                                                >
                                                    {
                                                        activeSubMenuData
                                                            .children.length
                                                    }{' '}
                                                    منتج
                                                </span>
                                            </div>
                                            <div className={styles.itemsGrid}>
                                                {activeSubMenuData.children.map(
                                                    (item) => (
                                                        <Link
                                                            key={item.id}
                                                            href={
                                                                show(item.slug)
                                                                    .url
                                                            }
                                                            className={
                                                                styles.itemLink
                                                            }
                                                        >
                                                            <div
                                                                className={
                                                                    styles.itemDot
                                                                }
                                                            />
                                                            <span>
                                                                {item.name}
                                                            </span>
                                                        </Link>
                                                    ),
                                                )}
                                            </div>
                                        </div>
                                    ) : (
                                        <div className={styles.emptyState}>
                                            <Grid3x3
                                                className={styles.emptyIcon}
                                            />
                                            <p className={styles.emptyText}>
                                                مرر الفأرة على الفئة الفرعية
                                                لعرض المزيد
                                            </p>
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>
                )}
        </div>
    );
};

export default MegaNavigation;
