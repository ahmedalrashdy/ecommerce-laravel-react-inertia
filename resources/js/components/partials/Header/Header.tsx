import { cn } from '@/lib/utils';
import { Link, usePage } from '@inertiajs/react';
import { Menu, ShoppingCart } from 'lucide-react';
import * as React from 'react';
import styles from './Header.module.css';
import { home } from '@/routes/store';
import { MegaNavigation } from './MegaNavigation';
import { MobileBottomNav } from './MobileBottomNav';
import { MobileMenu } from './MobileMenu';
import { SearchBar } from './SearchBar';
import { TopBar } from './TopBar';
import { UserActions } from './UserActions';
export default function Header() {
    const { mainCategories = [], settings } = usePage<{
        mainCategories: App.Data.Basic.CategoryData[];
        settings?: {
            general?: {
                store_name?: string;
                store_tagline?: string;
            };
        };
    }>().props;

    const storeName = settings?.general?.store_name || 'متجري';
    const storeTagline =
        settings?.general?.store_tagline || 'أفضل الأسعار دائماً';
    const [isMobileMenuOpen, setIsMobileMenuOpen] = React.useState(false);
    const headerRef = React.useRef<HTMLElement | null>(null);
    const lastScrollY = React.useRef(0);

    React.useEffect(() => {
        const header = headerRef.current;
        if (!header) {
            return;
        }

        let ticking = false;

        const updateScrollState = () => {
            const currentScrollY = window.scrollY;
            const isSticky = header.classList.contains(styles.headerSticky);
            const isHidden = header.classList.contains(styles.headerHidden);

            if (currentScrollY <= 0) {
                if (isSticky) {
                    header.classList.remove(styles.headerSticky);
                }
                if (isHidden) {
                    header.classList.remove(styles.headerHidden);
                    header.classList.add(styles.headerVisible);
                }
            } else {
                if (!isSticky) {
                    header.classList.add(styles.headerSticky);
                }

                if (
                    currentScrollY > lastScrollY.current &&
                    currentScrollY > 100
                ) {
                    if (!isHidden) {
                        header.classList.remove(styles.headerVisible);
                        header.classList.add(styles.headerHidden);
                    }
                } else if (currentScrollY < lastScrollY.current) {
                    if (isHidden) {
                        header.classList.remove(styles.headerHidden);
                        header.classList.add(styles.headerVisible);
                    }
                }
            }

            lastScrollY.current = currentScrollY;
            ticking = false;
        };

        const handleScroll = () => {
            if (!ticking) {
                ticking = true;
                window.requestAnimationFrame(updateScrollState);
            }
        };

        window.addEventListener('scroll', handleScroll, { passive: true });
        return () => window.removeEventListener('scroll', handleScroll);
    }, []);

    React.useEffect(() => {
        if (isMobileMenuOpen) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
        return () => {
            document.body.style.overflow = '';
        };
    }, [isMobileMenuOpen]);

    return (
        <>
            <header
                ref={headerRef}
                className={cn(styles.header, styles.headerVisible)}
            >
                <TopBar />

                <div className={styles.mainSection}>
                    <div className={styles.mainContainer}>
                        <div className={styles.mainInner}>
                            <div className={styles.leftSection}>
                                <div className="lg:hidden">
                                    <button
                                        onClick={() =>
                                            setIsMobileMenuOpen(true)
                                        }
                                        className={styles.mobileMenuButton}
                                    >
                                        <Menu
                                            className={styles.mobileMenuIcon}
                                        />
                                    </button>
                                </div>
                                <Link
                                    href={home.url()}
                                    className={styles.logoLink}
                                >
                                    <div className={styles.logoIconWrapper}>
                                        <ShoppingCart
                                            className={styles.logoIcon}
                                        />
                                        <div className={styles.logoOverlay} />
                                    </div>
                                    <div className={styles.logoTextWrapper}>
                                        <span className={styles.logoText}>
                                            {storeName}
                                        </span>
                                        {storeTagline && (
                                            <span
                                                className={styles.logoSubtext}
                                            >
                                                {storeTagline}
                                            </span>
                                        )}
                                    </div>
                                </Link>
                            </div>

                            <div className={styles.searchSection}>
                                <SearchBar />
                            </div>

                            <div className={styles.actionsSection}>
                                <UserActions />
                            </div>
                        </div>
                    </div>
                </div>

                <div className={styles.mobileSearchSection}>
                    <SearchBar placeholder="ابحث عن أي شيء..." />
                </div>
                <MegaNavigation categories={mainCategories} />
            </header>

            <MobileMenu
                isOpen={isMobileMenuOpen}
                onClose={() => setIsMobileMenuOpen(false)}
                categories={mainCategories}
            />

            <MobileBottomNav />

            {/* <div className={styles.spacer} /> */}
        </>
    );
}

export { Header };
