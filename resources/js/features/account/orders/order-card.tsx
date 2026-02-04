import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { cn, storageUrl } from '@/lib/utils';
import ordersRoutes from '@/routes/store/account/orders';
import paymentsRoutes from '@/routes/store/payments';
import { Link } from '@inertiajs/react';
import { CalendarDays } from 'lucide-react';

export type OrderSummary = {
    orderNumber: string;
    status: number;
    statusLabel: string;
    itemsCount: number;
    formattedGrandTotal: string;
    createdAt: string;
    createdAtIso: string;
    expectedDelivery: string | null;
    shippingName: string;
    trackingNumber: string | null;
    itemsPreview: Array<{
        id: number;
        name: string;
        image: string | null;
    }>;
    moreItemsCount: number;
    canPay: boolean;
    searchText: string;
};

const statusTone: Record<number, string> = {
    0: 'border-amber-200/60 bg-amber-50 text-amber-700 dark:border-amber-500/40 dark:bg-amber-500/10 dark:text-amber-200',
    1: 'border-sky-200/60 bg-sky-50 text-sky-700 dark:border-sky-500/40 dark:bg-sky-500/10 dark:text-sky-200',
    2: 'border-blue-200/60 bg-blue-50 text-blue-700 dark:border-blue-500/40 dark:bg-blue-500/10 dark:text-blue-200',
    3: 'border-indigo-200/60 bg-indigo-50 text-indigo-700 dark:border-indigo-500/40 dark:bg-indigo-500/10 dark:text-indigo-200',
    4: 'border-emerald-200/60 bg-emerald-50 text-emerald-700 dark:border-emerald-500/40 dark:bg-emerald-500/10 dark:text-emerald-200',
    5: 'border-rose-200/60 bg-rose-50 text-rose-700 dark:border-rose-500/40 dark:bg-rose-500/10 dark:text-rose-200',
    6: 'border-violet-200/60 bg-violet-50 text-violet-700 dark:border-violet-500/40 dark:bg-violet-500/10 dark:text-violet-200',
};

export function OrderCard({ order }: { order: OrderSummary }) {
    const orderDetailsUrl = ordersRoutes.show(order.orderNumber).url;

    return (
        <Card className="group relative overflow-hidden border-border/60 bg-card/80 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div className="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-primary/70 via-sky-500/40 to-transparent" />
            <div className="grid gap-6 p-6 lg:grid-cols-[1.4fr_1fr_1fr]">
                <div className="grid gap-4">
                    <div className="flex flex-wrap items-center gap-3">
                        <Badge
                            variant="outline"
                            className={cn(
                                'border text-xs',
                                statusTone[order.status] ??
                                    'border-border/60 text-foreground',
                            )}
                        >
                            {order.statusLabel}
                        </Badge>
                        <Link
                            href={orderDetailsUrl}
                            className="text-lg font-semibold text-primary hover:underline"
                        >
                            #{order.orderNumber}
                        </Link>
                    </div>
                    <div className="grid gap-3 text-sm sm:grid-cols-2">
                        <div className="grid gap-1">
                            <p className="text-xs text-muted-foreground">
                                تاريخ الطلب
                            </p>
                            <div className="flex items-center gap-2 font-semibold">
                                <CalendarDays className="h-4 w-4 text-muted-foreground" />
                                {order.createdAt}
                            </div>
                        </div>
                        <div className="grid gap-1">
                            <p className="text-xs text-muted-foreground">
                                الإجمالي
                            </p>
                            <p className="font-semibold">
                                {order.formattedGrandTotal}
                            </p>
                        </div>
                        <div className="grid gap-1">
                            <p className="text-xs text-muted-foreground">
                                شحن إلى
                            </p>
                            <p className="font-semibold">
                                {order.shippingName}
                            </p>
                        </div>
                        <div className="grid gap-1">
                            <p className="text-xs text-muted-foreground">
                                عدد المنتجات
                            </p>
                            <p className="font-semibold">
                                {order.itemsCount} عنصر
                            </p>
                        </div>
                    </div>
                </div>

                <div className="grid gap-3">
                    <p className="text-xs font-medium text-muted-foreground">
                        معاينة المنتجات
                    </p>
                    <div className="flex flex-wrap items-center gap-2">
                        {order.itemsPreview.map((item) => (
                            <div
                                key={item.id}
                                className="h-12 w-12 overflow-hidden rounded-xl border bg-muted"
                                title={item.name}
                            >
                                {item.image ? (
                                    <img
                                        src={storageUrl(item.image)}
                                        alt={item.name}
                                        className="h-full w-full object-cover"
                                        loading="lazy"
                                        decoding="async"
                                    />
                                ) : (
                                    <div className="flex h-full w-full items-center justify-center text-xs font-semibold text-muted-foreground">
                                        {item.name.slice(0, 2)}
                                    </div>
                                )}
                            </div>
                        ))}
                        {order.moreItemsCount > 0 && (
                            <div className="flex h-12 w-12 items-center justify-center rounded-full border bg-background text-xs font-semibold text-muted-foreground">
                                +{order.moreItemsCount}
                            </div>
                        )}
                    </div>
                    <div className="flex flex-wrap gap-2 text-xs text-muted-foreground">
                        <span className="rounded-full border border-border/60 bg-muted/30 px-2 py-1">
                            موعد الوصول المتوقع:{' '}
                            <span className="font-semibold text-foreground">
                                {order.expectedDelivery ?? 'غير متوفر'}
                            </span>
                        </span>
                        {order.trackingNumber && (
                            <span className="rounded-full border border-border/60 bg-muted/30 px-2 py-1">
                                رقم التتبع:{' '}
                                <span className="font-semibold text-foreground">
                                    {order.trackingNumber}
                                </span>
                            </span>
                        )}
                    </div>
                </div>

                <div className="flex flex-col gap-2">
                    {order.canPay && (
                        <Button
                            asChild
                            size="sm"
                        >
                            <Link
                                href={
                                    paymentsRoutes.start(order.orderNumber).url
                                }
                            >
                                إكمال الدفع
                            </Link>
                        </Button>
                    )}
                    <Button
                        asChild
                        size="sm"
                        variant="secondary"
                    >
                        <Link href={orderDetailsUrl}>عرض تفاصيل الطلب</Link>
                    </Button>
                    {order.trackingNumber && (
                        <Button
                            asChild
                            size="sm"
                            variant="outline"
                        >
                            <Link href={`${orderDetailsUrl}#tracking`}>
                                تتبع الشحنة
                            </Link>
                        </Button>
                    )}
                    <Button
                        asChild
                        size="sm"
                        variant="ghost"
                    >
                        <Link
                            method="post"
                            as="button"
                            href={ordersRoutes.reorder(order.orderNumber).url}
                        >
                            إعادة الطلب
                        </Link>
                    </Button>
                </div>
            </div>
        </Card>
    );
}
