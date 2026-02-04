import { Button } from '@/components/ui/button';
import { ReturnStatusBadge } from '@/features/account/returns/return-status-badge';
import returnsRoutes from '@/routes/store/account/returns';
import ordersRoutes from '@/routes/store/account/orders';
import { Link } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';

export function ReturnHeader({
    returnOrder,
}: {
    returnOrder: App.Data.Basic.ReturnDetailsData;
}) {
    return (
        <header className="flex flex-wrap items-start justify-between gap-4">
            <div className="space-y-2">
                <p className="text-sm text-muted-foreground">تفاصيل المرتجع</p>
                <div className="flex flex-wrap items-center gap-3">
                    <h1 className="text-3xl font-bold">
                        {returnOrder.returnNumber}
                    </h1>
                    <ReturnStatusBadge
                        status={returnOrder.status}
                        label={returnOrder.statusLabel}
                    />
                </div>
                <p className="text-sm text-muted-foreground">
                    تاريخ إنشاء الطلب: {returnOrder.createdAt}
                </p>
            </div>
            <div className="flex flex-wrap items-center gap-2">
                <Button
                    asChild
                    variant="outline"
                >
                    <Link
                        href={ordersRoutes.index.url({
                            query: { tab: 'returns' },
                        })}
                    >
                        <ArrowLeft className="h-4 w-4" />
                        الرجوع للمرتجعات
                    </Link>
                </Button>
                <Button
                    asChild
                    variant="outline"
                >
                    <Link
                        href={returnsRoutes.show(returnOrder.returnNumber).url}
                    >
                        تحديث الصفحة
                    </Link>
                </Button>
            </div>
        </header>
    );
}
