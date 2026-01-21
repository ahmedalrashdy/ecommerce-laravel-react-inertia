import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';

const returnStatusClasses: Record<number, string> = {
    1: 'border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-500/40 dark:bg-amber-500/10 dark:text-amber-200',
    2: 'border-sky-200 bg-sky-50 text-sky-800 dark:border-sky-500/40 dark:bg-sky-500/10 dark:text-sky-200',
    3: 'border-indigo-200 bg-indigo-50 text-indigo-800 dark:border-indigo-500/40 dark:bg-indigo-500/10 dark:text-indigo-200',
    4: 'border-blue-200 bg-blue-50 text-blue-800 dark:border-blue-500/40 dark:bg-blue-500/10 dark:text-blue-200',
    5: 'border-violet-200 bg-violet-50 text-violet-800 dark:border-violet-500/40 dark:bg-violet-500/10 dark:text-violet-200',
    6: 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-500/40 dark:bg-emerald-500/10 dark:text-emerald-200',
    7: 'border-rose-200 bg-rose-50 text-rose-800 dark:border-rose-500/40 dark:bg-rose-500/10 dark:text-rose-200',
};

export function ReturnStatusBadge({
    status,
    label,
}: {
    status: number;
    label: string;
}) {
    return (
        <Badge
            variant="outline"
            className={cn('border text-xs', returnStatusClasses[status])}
        >
            {label}
        </Badge>
    );
}
