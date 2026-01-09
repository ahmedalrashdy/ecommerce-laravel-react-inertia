import { cn } from '@/lib/utils';
import { Link } from '@inertiajs/react';
import { ChevronDown, X } from 'lucide-react';
import * as React from 'react';
import styles from './MobileMenu.module.css';

interface MobileMenuProps {
    isOpen: boolean;
    onClose: () => void;
    categories: App.Data.Basic.CategoryData[];
}

export const MobileMenu: React.FC<MobileMenuProps> = ({
    isOpen,
    onClose,
    categories,
}) => {
    const [expandedCategories, setExpandedCategories] = React.useState<
        Set<number>
    >(new Set());

    const toggleCategory = (categoryId: number) => {
        setExpandedCategories((prev) => {
            const newSet = new Set(prev);
            if (newSet.has(categoryId)) {
                newSet.delete(categoryId);
            } else {
                newSet.add(categoryId);
            }
            return newSet;
        });
    };

    const renderCategory = (
        category: App.Data.Basic.CategoryData,
        level: number = 0,
    ) => {
        const haschildren = category.children && category.children.length > 0;
        const isExpanded = expandedCategories.has(category.id);

        return (
            <div
                key={category.id}
                className={cn(level > 0 && styles.categoryItemIndented)}
            >
                <div className={styles.categoryHeader}>
                    <Link
                        href={`/products/${category.slug}`}
                        className={cn(
                            styles.categoryLink,
                            level === 0 && styles.categoryLinkLevel0,
                            level === 1 && styles.categoryLinkLevel1,
                            level >= 2 && styles.categoryLinkLevel2,
                        )}
                        onClick={onClose}
                    >
                        {category.name}
                    </Link>
                    {haschildren && (
                        <button
                            onClick={() => toggleCategory(category.id)}
                            className={styles.expandButton}
                        >
                            <ChevronDown
                                className={cn(
                                    styles.expandIcon,
                                    isExpanded && styles.expandIconOpen,
                                )}
                            />
                        </button>
                    )}
                </div>
                {haschildren && isExpanded && (
                    <div className={styles.subCategories}>
                        {category.children!.map((child) =>
                            renderCategory(child, level + 1),
                        )}
                    </div>
                )}
            </div>
        );
    };

    if (!isOpen) return null;

    return (
        <>
            <div
                className={styles.backdrop}
                onClick={onClose}
            />

            <div className={styles.sidebar}>
                <div className={styles.header}>
                    <h2 className={styles.headerTitle}>القائمة الرئيسية</h2>
                    <button
                        onClick={onClose}
                        className={styles.closeButton}
                    >
                        <X className={styles.closeIcon} />
                    </button>
                </div>

                <div className={styles.content}>
                    {categories.map((category) => renderCategory(category))}
                </div>
            </div>
        </>
    );
};

export default MobileMenu;
