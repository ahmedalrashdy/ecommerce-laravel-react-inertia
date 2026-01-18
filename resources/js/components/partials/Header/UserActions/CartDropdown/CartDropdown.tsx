import { SkeletonWrapper } from '@/components/feedback/SkeletonWrapper';
import { cn, storageUrl } from '@/lib/utils';
import { useCartStore } from '@/store/cart.store';
import { Link, usePage, WhenVisible } from '@inertiajs/react';
import { ShoppingCart } from 'lucide-react';
import * as React from 'react';
import styles from './CartDropdown.module.css';
import useClickOutside from '@/hooks/useClickOutSide';

export const CartDropdown: React.FC = () => {
    const count = useCartStore((state) => state.count());
    const [isOpen, setIsOpen] = React.useState(false);
    const cartRef = React.useRef<HTMLDivElement | null>(null);
    useClickOutside(cartRef, () => setIsOpen(false));

    return (
        <div className={styles.container}>
            {/* Cart Button - Desktop */}
            <div
                className={styles.cartButton}
                onClick={() => setIsOpen(true)}
            >
                <ShoppingCart className={styles.cartIcon} />
                {count > 0 && <span className={styles.cartBadge}>{count}</span>}
            </div>

            {/* Dropdown - Desktop Only */}
            {isOpen && (
                <div
                    className={styles.dropdown}
                    ref={cartRef}
                >
                    <WhenVisible
                        data="cart"
                        fallback={
                            <div className="p-4">
                                <SkeletonWrapper name="cartDropdown" />
                            </div>
                        }
                    >
                        <CartContent
                            globalCount={count}
                            onClose={() => setIsOpen(false)}
                        />
                    </WhenVisible>
                </div>
            )}
        </div>
    );
};

const CartContent = ({
    globalCount,
    onClose,
}: {
    globalCount: number;
    onClose: () => void;
}) => {
    const { cart: data } = usePage<{
        cart: App.Data.Basic.CartData;
    }>().props;
    const displayCount = data.itemsCount ?? globalCount;
    return (
        <>
            <div className={styles.dropdownHeader}>
                <h3 className={styles.dropdownTitle}>سلة التسوق</h3>
                <span className={styles.count}>{displayCount} عنصر</span>
            </div>

            <div className={styles.dropdownContent}>
                {data.items.length === 0 ? (
                    <div className={styles.emptyContainer}>
                        <ShoppingCart className={styles.emptyIcon} />
                        <p className={styles.emptyText}>السلة فارغة</p>
                    </div>
                ) : (
                    <div className={styles.itemsList}>
                        {data.items.map((item) => {
                            return (
                                <div
                                    key={item.id}
                                    className={cn(styles.item)}
                                >
                                    <img
                                        src={storageUrl(
                                            item.productVariant.defaultImage
                                                ?.path,
                                        )}
                                        alt={item.product.name}
                                        className={styles.itemImage}
                                    />
                                    <div className={styles.itemInfo}>
                                        <p className={styles.itemName}>
                                            {item.product.name}
                                        </p>
                                        <p className={styles.itemPrice}>
                                            {item.productVariant.price} ر.س ×{' '}
                                            {item.quantity}
                                        </p>
                                    </div>
                                </div>
                            );
                        })}
                        {data.items.length > 5 && (
                            <p className={styles.moreItems}>
                                و{data.items.length - 5} عنصر آخر...
                            </p>
                        )}
                    </div>
                )}
            </div>

            {data.items.length > 0 && (
                <>
                    <div className={styles.subtotalContainer}>
                        <span className={styles.subtotalLabel}>
                            المجموع الفرعي:
                        </span>
                        <span className={styles.subtotalValue}>
                            {data.formattedSubtotal}
                        </span>
                    </div>
                    <div className={styles.dropdownFooter}>
                        <Link
                            href="/cart"
                            className={styles.viewCartButton}
                            onClick={onClose}
                        >
                            عرض السلة
                        </Link>
                    </div>
                </>
            )}
        </>
    );
};
