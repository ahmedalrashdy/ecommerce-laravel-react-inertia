import { cn } from '@/lib/utils';
import { Link, usePage } from '@inertiajs/react';
import { ChevronLeft } from 'lucide-react';
import { accountNavItems } from './account-nav';

export default function AccountMobileMenu() {
    const { url } = usePage();

    return (
        <div className="lg:hidden">
            <div className="rounded-2xl border border-border/60 bg-card shadow-sm">
                <div className="border-b border-border/60 px-4 py-4">
                    <p className="text-xs text-muted-foreground">الحساب</p>
                    <h2 className="text-lg font-semibold">حسابي</h2>
                </div>
                <div className="divide-y divide-border/60">
                    {accountNavItems.map((item) => {
                        const isActive = item.isActive
                            ? item.isActive(url)
                            : url.startsWith(item.href);
                        const Icon = item.icon;

                        return (
                            <Link
                                key={item.key}
                                href={item.href}
                                className={cn(
                                    'flex items-center justify-between px-4 py-4 text-sm',
                                    isActive
                                        ? 'text-primary'
                                        : 'text-foreground/80',
                                )}
                            >
                                <div className="flex items-center gap-3">
                                    <Icon className="size-4" />
                                    <span className="font-medium">
                                        {item.label}
                                    </span>
                                </div>
                                <ChevronLeft className="size-4 text-muted-foreground" />
                            </Link>
                        );
                    })}
                </div>
            </div>
        </div>
    );
}
