import { cn } from '@/lib/utils';
import * as cartRoutes from '@/routes/store/cart';
import * as wishlistRoutes from '@/routes/store/wishlist';
import * as accountRoutes from '@/routes/store/account';
import { home } from '@/routes/store';
import { login } from '@/routes';
import { Link, usePage } from '@inertiajs/react';
import { Heart, Home, ShoppingCart, User } from 'lucide-react';
import { styles } from './MobileBottomNav.styles';
import { useCartStore } from '@/store/cart.store';
import { useWishlistStore } from '@/store/wishlist.store';
import { SharedData } from '@/types';

export const MobileBottomNav = () => {
    const cartCount = useCartStore((s) => s.count());
    const wishlistCount = useWishlistStore((s) => s.variants.length);
    const { auth } = usePage<SharedData>().props;
    const isAuthenticated = !!auth?.user;

    return (
        <div className={styles.wrapper}>
            <div className={styles.grid}>
                <Link
                    href={home.url()}
                    className={styles.navItem}
                >
                    <div className={styles.navItemIconWrapper}>
                        <Home className={styles.navItemIcon} />
                    </div>
                    <span>الرئيسية</span>
                </Link>
                <Link
                    href={cartRoutes.index.url()}
                    className={styles.navItem}
                >
                    <div className={styles.navItemIconWrapper}>
                        <ShoppingCart className={styles.navItemIcon} />
                        {cartCount > 0 && (
                            <span
                                className={cn(
                                    styles.badge,
                                    styles.badgeDestructive,
                                )}
                            >
                                {cartCount}
                            </span>
                        )}
                    </div>
                    <span>السلة</span>
                </Link>
                <Link
                    href={
                        isAuthenticated ? wishlistRoutes.index.url() : '/login'
                    }
                    className={styles.navItem}
                >
                    <div className={styles.navItemIconWrapper}>
                        <Heart className={styles.navItemIcon} />
                        {wishlistCount > 0 && (
                            <span
                                className={cn(
                                    styles.badge,
                                    styles.badgePrimary,
                                )}
                            >
                                {wishlistCount}
                            </span>
                        )}
                    </div>
                    <span>المفضلة</span>
                </Link>
                <Link
                    href={
                        isAuthenticated
                            ? accountRoutes.index.url()
                            : login.url()
                    }
                    className={styles.navItem}
                >
                    <div className={styles.navItemIconWrapper}>
                        <User className={styles.navItemIcon} />
                    </div>
                    <span>حسابي</span>
                </Link>
            </div>
        </div>
    );
};

export default MobileBottomNav;
