import { cn } from '@/lib/utils';
import { Link, usePage } from '@inertiajs/react';
import { accountNavItems } from './account-nav';

export default function AccountSidebar() {
    const { url } = usePage();

    return (
        <aside className="hidden lg:block">
            <div className="rounded-3xl border border-border/60 bg-card p-6 shadow-sm">
                <div className="mb-6 text-right">
                    <p className="text-xs text-muted-foreground">حسابي</p>
                    <h2 className="text-2xl font-semibold">حسابي</h2>
                </div>
                <nav className="space-y-1">
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
                                    'flex items-center justify-between rounded-2xl px-4 py-3 text-sm transition',
                                    isActive
                                        ? 'bg-primary/10 text-primary'
                                        : 'text-foreground/80 hover:bg-muted/60',
                                )}
                            >
                                <span className="font-medium">
                                    {item.label}
                                </span>
                                <Icon className="size-4" />
                            </Link>
                        );
                    })}
                </nav>
            </div>
        </aside>
    );
}
