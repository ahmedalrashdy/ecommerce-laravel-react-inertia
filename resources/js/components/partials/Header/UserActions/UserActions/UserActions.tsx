import * as React from 'react';
import { Form, Link, usePage } from '@inertiajs/react';
import {
    User,
    ChevronDown,
    LogIn,
    UserPlus,
    Package,
    HelpCircle,
    LogOut,
} from 'lucide-react';
import { cn } from '@/lib/utils';
import { CartDropdown } from '../CartDropdown/CartDropdown';
import { WishlistDropdown } from '../WishlistDropdown/WishlistDropdown';
import styles from './UserActions.module.css';
import { login, register, logout } from '@/routes';
import * as ordersRoutes from '@/routes/store/account/orders';
import { help } from '@/routes/store';
import { SharedData } from '@/types';

export const UserActions: React.FC = () => {
    const [isUserMenuOpen, setIsUserMenuOpen] = React.useState(false);
    const userMenuRef = React.useRef<HTMLDivElement>(null);
    const { auth } = usePage<SharedData>().props;
    const isAuthenticated = !!auth?.user;

    React.useEffect(() => {
        const handleClickOutside = (event: MouseEvent) => {
            if (
                userMenuRef.current &&
                !userMenuRef.current.contains(event.target as Node)
            ) {
                setIsUserMenuOpen(false);
            }
        };

        document.addEventListener('mousedown', handleClickOutside);
        return () =>
            document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    return (
        <div className={styles.wrapper}>
            {/* حسابي - Desktop */}
            <div
                className={styles.desktopUserMenuContainer}
                ref={userMenuRef}
            >
                <button
                    onClick={() => setIsUserMenuOpen(!isUserMenuOpen)}
                    className={styles.userMenuButton}
                >
                    <User className={styles.userMenuIcon} />
                    <span>حسابي</span>
                    <ChevronDown
                        className={cn(
                            styles.userMenuChevron,
                            isUserMenuOpen && styles.userMenuChevronOpen,
                        )}
                    />
                </button>

                {isUserMenuOpen && (
                    <div className={styles.userMenuDropdown}>
                        <div className={styles.userMenuDropdownInner}>
                            {!isAuthenticated ? (
                                <>
                                    <Link
                                        href={login.url()}
                                        className={cn(
                                            styles.userMenuLink,
                                            styles.userMenuLinkLogin,
                                        )}
                                        onClick={() => setIsUserMenuOpen(false)}
                                    >
                                        <div
                                            className={cn(
                                                styles.userMenuLinkIconWrapper,
                                                styles.userMenuLinkIconWrapperLogin,
                                            )}
                                        >
                                            <LogIn
                                                className={
                                                    styles.userMenuLinkIcon
                                                }
                                            />
                                        </div>
                                        <span
                                            className={styles.userMenuLinkText}
                                        >
                                            تسجيل الدخول
                                        </span>
                                    </Link>
                                    <Link
                                        href={register.url()}
                                        className={cn(
                                            styles.userMenuLink,
                                            styles.userMenuLinkRegister,
                                        )}
                                        onClick={() => setIsUserMenuOpen(false)}
                                    >
                                        <div
                                            className={cn(
                                                styles.userMenuLinkIconWrapper,
                                                styles.userMenuLinkIconWrapperRegister,
                                            )}
                                        >
                                            <UserPlus
                                                className={
                                                    styles.userMenuLinkIcon
                                                }
                                            />
                                        </div>
                                        <span
                                            className={styles.userMenuLinkText}
                                        >
                                            حساب جديد
                                        </span>
                                    </Link>
                                </>
                            ) : (
                                <>
                                    <Link
                                        href={ordersRoutes.index.url()}
                                        className={styles.userMenuLink}
                                        onClick={() => setIsUserMenuOpen(false)}
                                    >
                                        <div
                                            className={
                                                styles.userMenuLinkIconWrapper
                                            }
                                        >
                                            <Package
                                                className={
                                                    styles.userMenuLinkIcon
                                                }
                                            />
                                        </div>
                                        <span
                                            className={styles.userMenuLinkText}
                                        >
                                            طلباتي
                                        </span>
                                    </Link>
                                    <Link
                                        href="/account/profile"
                                        className={styles.userMenuLink}
                                        onClick={() => setIsUserMenuOpen(false)}
                                    >
                                        <div
                                            className={
                                                styles.userMenuLinkIconWrapper
                                            }
                                        >
                                            <User
                                                className={
                                                    styles.userMenuLinkIcon
                                                }
                                            />
                                        </div>
                                        <span
                                            className={styles.userMenuLinkText}
                                        >
                                            بياناتي الشخصية
                                        </span>
                                    </Link>
                                    <Link
                                        href={help.url()}
                                        className={cn(
                                            styles.userMenuLink,
                                            styles.userMenuLinkHelp,
                                        )}
                                        onClick={() => setIsUserMenuOpen(false)}
                                    >
                                        <div
                                            className={cn(
                                                styles.userMenuLinkIconWrapper,
                                                styles.userMenuLinkIconWrapperHelp,
                                            )}
                                        >
                                            <HelpCircle
                                                className={
                                                    styles.userMenuLinkIcon
                                                }
                                            />
                                        </div>
                                        <span
                                            className={styles.userMenuLinkText}
                                        >
                                            المساعدة
                                        </span>
                                    </Link>
                                    <Form
                                        action={logout.url()}
                                        method="post"
                                        className={styles.userMenuLinkForm}
                                        onSubmit={() =>
                                            setIsUserMenuOpen(false)
                                        }
                                    >
                                        <button
                                            type="submit"
                                            className={cn(
                                                styles.userMenuLink,
                                                styles.userMenuLinkLogout,
                                            )}
                                        >
                                            <div
                                                className={
                                                    styles.userMenuLinkIconWrapper
                                                }
                                            >
                                                <LogOut
                                                    className={
                                                        styles.userMenuLinkIcon
                                                    }
                                                />
                                            </div>
                                            <span
                                                className={
                                                    styles.userMenuLinkText
                                                }
                                            >
                                                تسجيل الخروج
                                            </span>
                                        </button>
                                    </Form>
                                </>
                            )}
                        </div>
                    </div>
                )}
            </div>

            {/* حسابي - Mobile */}
            <Link
                href={isAuthenticated ? ordersRoutes.index.url() : login.url()}
                className={styles.mobileUserLink}
            >
                <User className={styles.mobileUserIcon} />
            </Link>

            {/* المفضلة */}
            <WishlistDropdown />

            {/* عربة التسوق */}
            <CartDropdown />
        </div>
    );
};

export default UserActions;
