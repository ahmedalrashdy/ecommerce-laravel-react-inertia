import type { LucideIcon } from 'lucide-react';
import { Bell, MapPin, Package, RotateCcw, User } from 'lucide-react';

export type AccountNavItem = {
    key: string;
    label: string;
    href: string;
    icon: LucideIcon;
    isActive?: (url: string) => boolean;
};

export const accountNavItems: AccountNavItem[] = [
    {
        key: 'orders',
        label: 'طلباتي',
        href: '/account/orders',
        icon: Package,
        isActive: (url) =>
            url.startsWith('/account/orders') && !url.includes('tab=returns'),
    },
    {
        key: 'returns',
        label: 'مرتجعاتي',
        href: '/account/orders?tab=returns',
        icon: RotateCcw,
        isActive: (url) =>
            url.startsWith('/account/returns') || url.includes('tab=returns'),
    },
    {
        key: 'addresses',
        label: 'بيانات عنواني',
        href: '/account/addresses',
        icon: MapPin,
        isActive: (url) => url.startsWith('/account/addresses'),
    },
    {
        key: 'profile',
        label: 'بياناتي الشخصية',
        href: '/account/profile',
        icon: User,
        isActive: (url) => url.startsWith('/account/profile'),
    },
    {
        key: 'notifications',
        label: 'خيارات الإشعارات',
        href: '/account/notifications',
        icon: Bell,
        isActive: (url) => url.startsWith('/account/notifications'),
    },
];
