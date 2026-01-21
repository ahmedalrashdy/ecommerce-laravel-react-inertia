import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    ReturnHeader,
    ReturnItemCard,
    ReturnSummaryCard,
    ReturnTimeline,
} from '@/features/account/returns';
import { Head, usePage } from '@inertiajs/react';
import AccountLayout from '@/features/account/layout/AccountLayout';
import type { ReactNode } from 'react';

function ReturnDetailsPage() {
    const { returnOrder } = usePage<{
        returnOrder: App.Data.Basic.ReturnDetailsData;
    }>().props;

    return (
        <>
            <Head title={`مرتجع ${returnOrder.returnNumber}`} />
            <div className="flex flex-col gap-8">
                <ReturnHeader returnOrder={returnOrder} />

                <div className="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
                    <div className="space-y-6">
                        <Card>
                            <CardHeader className="flex flex-row items-center justify-between">
                                <CardTitle className="text-base">
                                    عناصر المرتجع
                                </CardTitle>
                                <span className="text-sm text-muted-foreground">
                                    {returnOrder.itemsCount} عنصر
                                </span>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                {returnOrder.items.map((item) => (
                                    <ReturnItemCard
                                        key={item.id}
                                        item={item}
                                    />
                                ))}
                            </CardContent>
                        </Card>

                        <ReturnTimeline timeline={returnOrder.timeline} />
                    </div>

                    <div className="space-y-6">
                        <ReturnSummaryCard returnOrder={returnOrder} />

                        <Card>
                            <CardHeader>
                                <CardTitle className="text-base">
                                    معلومات إضافية
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-3 text-sm text-muted-foreground">
                                <div className="flex items-center justify-between">
                                    <span>رقم المرتجع</span>
                                    <span className="font-semibold text-foreground">
                                        {returnOrder.returnNumber}
                                    </span>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span>رقم الطلب</span>
                                    <span className="font-semibold text-foreground">
                                        {returnOrder.orderNumber ?? '—'}
                                    </span>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span>تاريخ الإنشاء</span>
                                    <span className="font-semibold text-foreground">
                                        {returnOrder.createdAt}
                                    </span>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span>الحالة الحالية</span>
                                    <span className="font-semibold text-foreground">
                                        {returnOrder.statusLabel}
                                    </span>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </>
    );
}

ReturnDetailsPage.layout = (page: ReactNode) => (
    <AccountLayout>{page}</AccountLayout>
);

export default ReturnDetailsPage;
