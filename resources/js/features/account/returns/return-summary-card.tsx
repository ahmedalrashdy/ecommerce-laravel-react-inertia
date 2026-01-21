import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { ReturnStatusBadge } from '@/features/account/returns/return-status-badge';
import { BadgeDollarSign, Package, Truck } from 'lucide-react';

export function ReturnSummaryCard({
    returnOrder,
}: {
    returnOrder: App.Data.Basic.ReturnDetailsData;
}) {
    return (
        <Card>
            <CardHeader className="flex flex-row items-center justify-between">
                <CardTitle className="text-base">ملخص المرتجع</CardTitle>
                <ReturnStatusBadge
                    status={returnOrder.status}
                    label={returnOrder.statusLabel}
                />
            </CardHeader>
            <CardContent className="space-y-4 text-sm text-muted-foreground">
                <div className="flex items-center justify-between">
                    <span className="flex items-center gap-2">
                        <Package className="h-4 w-4" />
                        الطلب الأصلي
                    </span>
                    <span className="font-semibold text-foreground">
                        {returnOrder.orderNumber ?? '—'}
                    </span>
                </div>
                <div className="flex items-center justify-between">
                    <span className="flex items-center gap-2">
                        <BadgeDollarSign className="h-4 w-4" />
                        طريقة الاسترجاع
                    </span>
                    <span className="font-semibold text-foreground">
                        {returnOrder.refundMethodLabel ?? '—'}
                    </span>
                </div>
                <div className="flex items-center justify-between">
                    <span className="flex items-center gap-2">
                        <Truck className="h-4 w-4" />
                        رقم التتبع
                    </span>
                    <span className="font-semibold text-foreground">
                        {returnOrder.trackingNumber ?? '—'}
                    </span>
                </div>
                <div className="rounded-lg border bg-muted/40 p-3">
                    <div className="flex items-center justify-between">
                        <span>المبلغ المتوقع</span>
                        <span className="text-base font-semibold text-foreground">
                            {returnOrder.formattedRefundAmount}
                        </span>
                    </div>
                </div>
                {returnOrder.shippingLabelUrl && (
                    <a
                        href={returnOrder.shippingLabelUrl}
                        className="inline-flex items-center gap-2 text-xs font-semibold text-primary"
                        target="_blank"
                        rel="noreferrer"
                    >
                        تحميل بوليصة الشحن
                    </a>
                )}
            </CardContent>
        </Card>
    );
}
