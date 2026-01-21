import { Button } from '@/components/ui/button';
import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import { cn } from '@/lib/utils';
import { Link } from '@inertiajs/react';
import type { LucideIcon } from 'lucide-react';

type OrdersEmptyStateProps = {
    title: string;
    description: string;
    icon: LucideIcon;
    actionLabel?: string;
    actionHref?: string;
};

export function OrdersEmptyState({
    title,
    description,
    icon: Icon,
    actionLabel,
    actionHref,
}: OrdersEmptyStateProps) {
    return (
        <div className="relative overflow-hidden rounded-3xl border bg-card p-8">
            <PlaceholderPattern className="absolute inset-0 opacity-10" />
            <div className="relative flex flex-col items-center gap-3 text-center">
                <div
                    className={cn(
                        'flex h-14 w-14 items-center justify-center rounded-2xl',
                        'bg-primary/10 text-primary',
                    )}
                >
                    <Icon className="h-6 w-6" />
                </div>
                <div className="space-y-1">
                    <h3 className="text-lg font-semibold">{title}</h3>
                    <p className="text-sm text-muted-foreground">
                        {description}
                    </p>
                </div>
                {actionLabel && actionHref ? (
                    <Link href={actionHref}>
                        <Button size="sm">{actionLabel}</Button>
                    </Link>
                ) : null}
            </div>
        </div>
    );
}
