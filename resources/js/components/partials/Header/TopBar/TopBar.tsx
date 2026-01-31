import { useAppearance } from '@/hooks/use-appearance';
import { cn } from '@/lib/utils';
import { Link, usePage } from '@inertiajs/react';
import {
    ChevronDown,
    Laptop,
    Mail,
    Moon,
    Package,
    Phone,
    Sun,
} from 'lucide-react';
import * as React from 'react';
import { styles } from './TopBar.styles';

export const TopBar: React.FC = () => {
    const { settings } = usePage<{
        settings?: {
            contact?: {
                phone?: string;
                email?: string;
            };
        };
    }>().props;

    const phone = settings?.contact?.phone || '+966 12 345 6789';
    const email = settings?.contact?.email || 'info@store.com';

    const [isThemeMenuOpen, setIsThemeMenuOpen] = React.useState(false);
    const themeMenuRef = React.useRef<HTMLDivElement>(null);

    React.useEffect(() => {
        const handleClickOutside = (event: MouseEvent) => {
            if (
                themeMenuRef.current &&
                !themeMenuRef.current.contains(event.target as Node)
            ) {
                setIsThemeMenuOpen(false);
            }
        };

        document.addEventListener('mousedown', handleClickOutside);
        return () =>
            document.removeEventListener('mousedown', handleClickOutside);
    }, []);
    const { appearance, updateAppearance } = useAppearance();

    return (
        <div className={styles.topBar}>
            <div className={styles.container}>
                <div className={styles.inner}>
                    <div className={styles.leftSection}>
                        <div className={styles.contactItem}>
                            <Phone className={styles.contactIcon} />
                            <a
                                href={`tel:${phone.replace(/\s/g, '')}`}
                                dir="ltr"
                                className={styles.contactText}
                            >
                                {phone}
                            </a>
                        </div>
                        <div className={styles.divider} />
                        <div className={styles.contactItem}>
                            <Mail className={styles.contactIcon} />
                            <a
                                href={`mailto:${email}`}
                                className={styles.contactText}
                            >
                                {email}
                            </a>
                        </div>
                        <div className={styles.divider} />
                    </div>

                    <div className={styles.rightSection}>
                        <Link
                            href="/account/orders"
                            className={styles.trackOrderLink}
                        >
                            <Package className={styles.trackOrderIcon} />
                            <span className={styles.trackOrderText}>
                                تتبع طلبك
                            </span>
                        </Link>
                        <div className={styles.divider} />
                        <Link
                            href="/help"
                            className={styles.helpLink}
                        >
                            المساعدة
                        </Link>
                        <div className={styles.divider} />

                        <div
                            className={styles.themeToggleContainer}
                            ref={themeMenuRef}
                        >
                            <button
                                onClick={() =>
                                    setIsThemeMenuOpen(!isThemeMenuOpen)
                                }
                                className={styles.themeToggleButton}
                            >
                                {appearance === 'light' && (
                                    <Sun className={styles.themeIcon} />
                                )}
                                {appearance === 'dark' && (
                                    <Moon className={styles.themeIcon} />
                                )}
                                {appearance === 'system' && (
                                    <Laptop className={styles.themeIcon} />
                                )}
                                <ChevronDown
                                    className={cn(
                                        styles.chevronIcon,
                                        isThemeMenuOpen &&
                                            styles.chevronIconOpen,
                                    )}
                                />
                            </button>
                            {isThemeMenuOpen && (
                                <div className={styles.themeMenu}>
                                    <div className={styles.themeMenuInner}>
                                        <button
                                            onClick={() => {
                                                updateAppearance('light');
                                                setIsThemeMenuOpen(false);
                                            }}
                                            className={cn(
                                                styles.themeMenuItem,
                                                appearance === 'light' &&
                                                    styles.themeMenuItemActive,
                                            )}
                                        >
                                            <Sun
                                                className={
                                                    styles.themeMenuItemIcon
                                                }
                                            />
                                            <span>فاتح</span>
                                        </button>
                                        <button
                                            onClick={() => {
                                                updateAppearance('dark');
                                                setIsThemeMenuOpen(false);
                                            }}
                                            className={cn(
                                                styles.themeMenuItem,
                                                appearance === 'dark' &&
                                                    styles.themeMenuItemActive,
                                            )}
                                        >
                                            <Moon
                                                className={
                                                    styles.themeMenuItemIcon
                                                }
                                            />
                                            <span>داكن</span>
                                        </button>
                                        <button
                                            onClick={() => {
                                                updateAppearance('system');
                                                setIsThemeMenuOpen(false);
                                            }}
                                            className={cn(
                                                styles.themeMenuItem,
                                                appearance === 'system' &&
                                                    styles.themeMenuItemActive,
                                            )}
                                        >
                                            <Laptop
                                                className={
                                                    styles.themeMenuItemIcon
                                                }
                                            />
                                            <span>النظام</span>
                                        </button>
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};
export default TopBar;
