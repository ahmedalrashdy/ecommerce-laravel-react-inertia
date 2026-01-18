import { SkeletonWrapper } from '@/components/feedback/SkeletonWrapper';
import { cn, storageUrl } from '@/lib/utils';
import { login } from '@/routes';
import { useWishlistStore } from '@/store/wishlist.store';
import { Link, usePage, WhenVisible } from '@inertiajs/react';
import { Heart } from 'lucide-react';
import * as React from 'react';
import styles from './WishlistDropdown.module.css';
import useClickOutside from '@/hooks/useClickOutSide';

export const WishlistDropdown: React.FC = () => {
    const itemsCount = useWishlistStore((state) => state.variants?.length);
    const { auth } = usePage<{ auth?: { user?: unknown } }>().props;
    const [isOpen, setIsOpen] = React.useState(false);
    const isAuthenticated = Boolean(auth?.user);
    const wishlistRef = React.useRef<HTMLDivElement | null>(null);
    useClickOutside(wishlistRef, () => setIsOpen(false));
    return (
        <div className={styles.container}>
            {/* Wishlist Button */}
            <div
                className={styles.wishlistButton}
                onClick={() => setIsOpen(true)}
            >
                <Heart
                    className={cn(
                        styles.wishlistIcon,
                        itemsCount > 0 && styles.wishlistIconFilled,
                    )}
                />
                {itemsCount > 0 && (
                    <span className={styles.wishlistBadge}>{itemsCount}</span>
                )}
            </div>

            {/* Dropdown Content */}
            {isOpen && (
                <div
                    className={styles.dropdown}
                    ref={wishlistRef}
                >
                    {isAuthenticated ? (
                        <WhenVisible
                            data="wishlistDropdown"
                            fallback={
                                <div className="p-4">
                                    <SkeletonWrapper name="cartDropdown" />
                                </div>
                            }
                        >
                            <WishlistContent
                                globalCount={itemsCount}
                                onClose={() => setIsOpen(false)}
                            />
                        </WhenVisible>
                    ) : (
                        <GuestWishlistContent
                            onClose={() => setIsOpen(false)}
                        />
                    )}
                </div>
            )}
        </div>
    );
};

const WishlistContent = ({
    globalCount,
    onClose,
}: {
    globalCount: number;
    onClose: () => void;
}) => {
    const { wishlistDropdown } = usePage<{
        wishlistDropdown?: App.Data.Basic.WishlistData;
    }>().props;
    const data = wishlistDropdown ?? { items: [], itemsCount: 0 };
    const displayCount = data.itemsCount ?? globalCount;
    return (
        <>
            <div className={styles.dropdownHeader}>
                <h3 className={styles.dropdownTitle}>المفضلة</h3>
                <span className={styles.itemsCount}>{displayCount} عنصر</span>
            </div>

            <div className={styles.dropdownContent}>
                {data.items.length === 0 ? (
                    <div className={styles.emptyContainer}>
                        <Heart className={styles.emptyIcon} />
                        <p className={styles.emptyText}>المفضلة فارغة</p>
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
                                            {item.productVariant.price} ر.س
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
                <div className={styles.dropdownFooter}>
                    <Link
                        href="/wishlist"
                        className={styles.viewWishlistButton}
                        onClick={onClose}
                    >
                        عرض المفضلة
                    </Link>
                </div>
            )}
        </>
    );
};

const GuestWishlistContent = ({ onClose }: { onClose: () => void }) => {
    return (
        <>
            <div className={styles.dropdownHeader}>
                <h3 className={styles.dropdownTitle}>المفضلة</h3>
            </div>
            <div className={styles.dropdownContent}>
                <div className={styles.emptyContainer}>
                    <Heart className={styles.emptyIcon} />
                    <p className={styles.emptyText}>
                        سجّل الدخول لعرض المفضلة الخاصة بك
                    </p>
                </div>
            </div>
            <div className={styles.dropdownFooter}>
                <Link
                    href={login()}
                    className={styles.viewWishlistButton}
                    onClick={onClose}
                >
                    تسجيل الدخول
                </Link>
            </div>
        </>
    );
};
