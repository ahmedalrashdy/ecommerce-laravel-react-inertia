import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardFooter, CardHeader } from '@/components/ui/card';
import { cn } from '@/lib/utils';
import returnsRoutes from '@/routes/store/account/returns';
import { Link } from '@inertiajs/react';
import { Calendar, Package, RotateCcw } from 'lucide-react';

export type ReturnSummary = {
    id: number;
    returnNumber: string;
    orderNumber: string | null;
    status: number;
    statusLabel: string;
    itemsCount: number;
    formattedRefundAmount: string;
    createdAt: string;
};

const returnStatusClasses: Record<number, string> = {
    1: 'border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-500/40 dark:bg-amber-500/10 dark:text-amber-200',
    2: 'border-sky-200 bg-sky-50 text-sky-800 dark:border-sky-500/40 dark:bg-sky-500/10 dark:text-sky-200',
    3: 'border-indigo-200 bg-indigo-50 text-indigo-800 dark:border-indigo-500/40 dark:bg-indigo-500/10 dark:text-indigo-200',
    4: 'border-blue-200 bg-blue-50 text-blue-800 dark:border-blue-500/40 dark:bg-blue-500/10 dark:text-blue-200',
    5: 'border-violet-200 bg-violet-50 text-violet-800 dark:border-violet-500/40 dark:bg-violet-500/10 dark:text-violet-200',
    6: 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-500/40 dark:bg-emerald-500/10 dark:text-emerald-200',
    7: 'border-rose-200 bg-rose-50 text-rose-800 dark:border-rose-500/40 dark:bg-rose-500/10 dark:text-rose-200',
};

export function ReturnCard({ returnOrder }: { returnOrder: ReturnSummary }) {
    return (
        <Card className="border-border/60 bg-card/80 transition hover:-translate-y-0.5 hover:shadow-md">
            <CardHeader className="gap-3">
                <div className="flex items-start justify-between gap-4">
                    <div>
                        <p className="text-xs text-muted-foreground">
                            رقم المرتجع
                        </p>
                        <Link
                            href={returnsRoutes.show(returnOrder.id).url}
                            className="text-lg font-semibold text-foreground hover:text-primary"
                        >
                            {returnOrder.returnNumber}
                        </Link>
                    </div>
                    <Badge
                        variant="outline"
                        className={cn(
                            'border text-xs',
                            returnStatusClasses[returnOrder.status],
                        )}
                    >
                        {returnOrder.statusLabel}
                    </Badge>
                </div>
                <div className="flex flex-wrap items-center gap-3 text-xs text-muted-foreground">
                    <span className="flex items-center gap-2">
                        <Calendar className="h-4 w-4" />
                        {returnOrder.createdAt}
                    </span>
                    <span className="flex items-center gap-2">
                        <Package className="h-4 w-4" />
                        {returnOrder.itemsCount} عنصر
                    </span>
                    <span className="flex items-center gap-2">
                        <RotateCcw className="h-4 w-4" />
                        طلب #{returnOrder.orderNumber ?? '—'}
                    </span>
                </div>
            </CardHeader>
            <CardContent>
                <div className="flex items-center justify-between rounded-lg bg-muted/40 px-3 py-2 text-sm">
                    <span className="text-muted-foreground">المبلغ المتوقع</span>
                    <span className="font-semibold">
                        {returnOrder.formattedRefundAmount}
                    </span>
                </div>
            </CardContent>
            <CardFooter>
                <p className="text-xs text-muted-foreground">
                    سنعرض أي تحديث جديد للمرتجع هنا فور حدوثه.
                </p>
            </CardFooter>
        </Card>
    );
}
