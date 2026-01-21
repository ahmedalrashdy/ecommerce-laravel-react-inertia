import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import StoreLayout from '@/layouts/StoreLayout';
import ordersRoutes from '@/routes/store/account/orders';
import { Head, Link, usePage } from '@inertiajs/react';

type PaymentStatusProps = {
    order: {
        id: number;
        orderNumber: string;
        paymentStatusLabel: string;
        statusLabel: string;
    };
};

export default function PaymentSuccess() {
    const { order } = usePage<PaymentStatusProps>().props;

    return (
        <StoreLayout>
            <Head title="تم استلام عملية الدفع" />
            <div className="relative overflow-hidden">
                <div
                    className="absolute -top-24 right-0 h-64 w-64 rounded-full bg-emerald-200/40 blur-3xl"
                    aria-hidden="true"
                />
                <div
                    className="absolute -bottom-24 left-0 h-64 w-64 rounded-full bg-sky-200/40 blur-3xl"
                    aria-hidden="true"
                />
                <div className="container mx-auto px-4 py-12">
                    <div className="mx-auto max-w-3xl">
                        <Card className="border-emerald-200/60 bg-white/85 shadow-2xl shadow-emerald-500/10 backdrop-blur">
                            <CardHeader className="items-center gap-4 text-center">
                                <div className="relative flex h-20 w-20 items-center justify-center">
                                    <span className="absolute inset-0 rounded-full bg-emerald-400/30 motion-reduce:animate-none animate-ping" />
                                    <span className="absolute inset-0 rounded-full bg-emerald-500/15 motion-reduce:animate-none animate-pulse" />
                                    <span className="relative flex h-16 w-16 items-center justify-center rounded-full bg-emerald-500 text-white shadow-lg shadow-emerald-500/40">
                                        <svg
                                            viewBox="0 0 24 24"
                                            className="h-9 w-9"
                                            fill="none"
                                            stroke="currentColor"
                                            strokeWidth="2.5"
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            aria-hidden="true"
                                        >
                                            <path d="M20 6L9 17l-5-5" />
                                        </svg>
                                    </span>
                                </div>
                                <div className="space-y-2">
                                    <CardTitle className="text-2xl text-emerald-900">
                                        تم استلام عملية الدفع بنجاح
                                    </CardTitle>
                                    <p className="text-sm text-muted-foreground">
                                        جاري تأكيد العملية من مزود الدفع. سنرسل لك إشعارًا فور اكتمال التأكيد.
                                    </p>
                                </div>
                            </CardHeader>
                            <CardContent className="space-y-6">
                                <div className="rounded-2xl border border-emerald-200/60 bg-emerald-50/60 p-4 text-sm">
                                    <div className="flex flex-wrap items-center justify-between gap-2">
                                        <span className="text-muted-foreground">
                                            رقم الطلب
                                        </span>
                                        <span className="rounded-full bg-white px-3 py-1 font-semibold text-emerald-900 shadow-sm">
                                            #{order.orderNumber}
                                        </span>
                                    </div>
                                </div>

                                <div className="flex flex-col gap-3 sm:flex-row sm:justify-center">
                                    <Button asChild className="sm:min-w-[160px]">
                                        <Link href={ordersRoutes.show(order.id).url}>
                                            عرض الطلب
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="outline"
                                        asChild
                                        className="sm:min-w-[160px]"
                                    >
                                        <Link href={ordersRoutes.index().url}>
                                            طلباتي
                                        </Link>
                                    </Button>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </StoreLayout>
    );
}
