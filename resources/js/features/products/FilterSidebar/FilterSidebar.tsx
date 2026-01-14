import { Checkbox } from '@/components/ui/checkbox';
import { Slider } from '@/components/ui/slider';
import { ChevronDown, SlidersHorizontal, X } from 'lucide-react';
import * as React from 'react';

import { cn } from '@/lib/utils';
import styles from './FilterSidebar.module.css';

export interface FilterSidebarProps {
    categories: App.Data.Basic.CategoryData[];
    brands: App.Data.Basic.BrandData[];
    priceRange: [number, number];
    maxPrice: number;
    onPriceChange: (range: [number, number]) => void;
    selectedCategories: string[];
    onCategoryChange: (slugs: string[]) => void;
    selectedBrands: string[];
    onBrandChange: (slugs: string[]) => void;
    onClearAll: () => void;
    currentCategorySlug?: string | null;
    currentBrandSlug?: string | null;
}

export const FilterSidebar: React.FC<FilterSidebarProps> = ({
    categories,
    brands,
    priceRange,
    maxPrice,
    onPriceChange,
    selectedCategories,
    onCategoryChange,
    selectedBrands,
    onBrandChange,
    onClearAll,
    currentCategorySlug,
    currentBrandSlug,
}) => {
    const [expandedCategories, setExpandedCategories] = React.useState<
        Set<number>
    >(new Set([1]));

    const toggleCategory = (id: number) => {
        setExpandedCategories((prev) => {
            const next = new Set(prev);
            if (next.has(id)) next.delete(id);
            else next.add(id);
            return next;
        });
    };

    const isCategorySelected = (slug: string) => {
        return (
            selectedCategories.includes(slug) || currentCategorySlug === slug
        );
    };

    const isBrandSelected = (slug: string) => {
        return selectedBrands.includes(slug) || currentBrandSlug === slug;
    };

    const handleCategoryToggle = (slug: string, checked: boolean) => {
        if (checked) {
            onCategoryChange([...selectedCategories, slug]);
        } else {
            onCategoryChange(selectedCategories.filter((s) => s !== slug));
        }
    };

    const handleBrandToggle = (slug: string, checked: boolean) => {
        if (checked) {
            onBrandChange([...selectedBrands, slug]);
        } else {
            onBrandChange(selectedBrands.filter((s) => s !== slug));
        }
    };

    const hasFilters =
        selectedCategories.length > 0 ||
        selectedBrands.length > 0 ||
        priceRange[0] > 0 ||
        priceRange[1] < maxPrice;

    return (
        <div className={styles.container}>
            {/* Header */}
            <div className={styles.header}>
                <h3 className={styles.headerTitle}>
                    <SlidersHorizontal className={styles.headerIcon} />
                    الفلاتر
                </h3>
                {hasFilters && (
                    <button
                        onClick={onClearAll}
                        className={styles.clearButton}
                    >
                        <X className={styles.clearIcon} />
                        مسح الكل
                    </button>
                )}
            </div>

            {/* Categories */}
            <div className={styles.section}>
                <h4 className={styles.sectionTitle}>
                    الفئات
                    <span className={styles.badge}>{categories.length}</span>
                </h4>
                <div className={styles.categoriesList}>
                    {categories.map((category) => (
                        <div
                            key={category.id}
                            className="group"
                        >
                            <div className={styles.categoryItem}>
                                <Checkbox
                                    id={`cat-${category.slug}`}
                                    checked={isCategorySelected(category.slug)}
                                    disabled={
                                        currentCategorySlug === category.slug
                                    }
                                    onCheckedChange={(checked) =>
                                        handleCategoryToggle(
                                            category.slug,
                                            !!checked,
                                        )
                                    }
                                    className="border-muted-foreground/40 data-[state=checked]:border-primary"
                                />
                                <label
                                    htmlFor={`cat-${category.slug}`}
                                    className={styles.categoryLabel}
                                >
                                    {category.name}
                                </label>
                                <span className={styles.categoryCount}>
                                    ({category.productsCount})
                                </span>
                                {category.children &&
                                    category.children.length > 0 && (
                                        <button
                                            onClick={() =>
                                                toggleCategory(category.id)
                                            }
                                            className={styles.expandButton}
                                        >
                                            <ChevronDown
                                                className={cn(
                                                    styles.expandIcon,
                                                    expandedCategories.has(
                                                        category.id,
                                                    ) &&
                                                        styles.expandIconRotated,
                                                )}
                                            />
                                        </button>
                                    )}
                            </div>
                            {category.children &&
                                expandedCategories.has(category.id) && (
                                    <div className={styles.childrenContainer}>
                                        {category.children.map((child) => (
                                            <div
                                                key={child.id}
                                                className={styles.childItem}
                                            >
                                                <Checkbox
                                                    id={`cat-${child.slug}`}
                                                    checked={isCategorySelected(
                                                        child.slug,
                                                    )}
                                                    disabled={
                                                        currentCategorySlug ===
                                                        child.slug
                                                    }
                                                    onCheckedChange={(
                                                        checked,
                                                    ) =>
                                                        handleCategoryToggle(
                                                            child.slug,
                                                            !!checked,
                                                        )
                                                    }
                                                    className="h-3.5 w-3.5"
                                                />
                                                <label
                                                    htmlFor={`cat-${child.slug}`}
                                                    className={
                                                        styles.childLabel
                                                    }
                                                >
                                                    {child.name}
                                                </label>
                                                <span
                                                    className={
                                                        styles.childCount
                                                    }
                                                >
                                                    {child.productsCount}
                                                </span>
                                            </div>
                                        ))}
                                    </div>
                                )}
                        </div>
                    ))}
                </div>
            </div>

            {/* Price Range */}
            <div className={styles.priceSection}>
                <h4 className={styles.priceTitle}>نطاق السعر</h4>
                <div className={styles.priceSliderContainer}>
                    <Slider
                        value={priceRange}
                        min={0}
                        max={maxPrice}
                        step={50}
                        onValueCommit={(value) =>
                            onPriceChange(value as [number, number])
                        }
                        // onValueChange=
                        className={styles.priceSlider}
                    />
                    <div className={styles.priceInputs}>
                        <div className={styles.priceInput}>
                            <span className={styles.priceInputLabel}>من</span>
                            <span className={styles.priceInputValue}>
                                {priceRange[0]} ر.س
                            </span>
                        </div>
                        <div className={styles.priceInput}>
                            <span className={styles.priceInputLabel}>إلى</span>
                            <span className={styles.priceInputValue}>
                                {priceRange[1]} ر.س
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {/* Brands */}
            <div className={styles.brandsSection}>
                <h4 className={styles.sectionTitle}>
                    العلامات التجارية
                    <span className={styles.badge}>{brands.length}</span>
                </h4>
                <div className={styles.brandsList}>
                    {brands.map((brand) => (
                        <div
                            key={brand.id}
                            className={styles.brandItem}
                        >
                            <Checkbox
                                id={`brand-${brand.slug}`}
                                checked={isBrandSelected(brand.slug)}
                                disabled={currentBrandSlug === brand.slug}
                                onCheckedChange={(checked) =>
                                    handleBrandToggle(brand.slug, !!checked)
                                }
                            />
                            <label
                                htmlFor={`brand-${brand.slug}`}
                                className={styles.brandLabel}
                            >
                                {brand.name}
                            </label>
                            <span className={styles.brandCount}>
                                {brand.productsCount}
                            </span>
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );
};
