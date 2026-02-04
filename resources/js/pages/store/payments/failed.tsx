import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import StoreLayout from '@/layouts/StoreLayout';
import ordersRoutes from '@/routes/store/account/orders';
import paymentsRoutes from '@/routes/store/payments';
import { Head, Link, usePage } from '@inertiajs/react';

type PaymentStatusProps = {
    order: {
        id: number;
        orderNumber: string;
        paymentStatusLabel: string;
        statusLabel: string;
    };
    errors: {
        payment?: string;
    };
};

export default function PaymentFailed() {
    const { order, errors } = usePage<PaymentStatusProps>().props;

    return (
        <StoreLayout>
            <Head title="لم تكتمل عملية الدفع" />
            <div className="relative overflow-hidden">
                <div
                    className="absolute -top-28 left-8 h-56 w-56 rounded-full bg-rose-200/40 blur-3xl"
                    aria-hidden="true"
                />
                <div
                    className="absolute right-0 -bottom-24 h-64 w-64 rounded-full bg-amber-200/40 blur-3xl"
                    aria-hidden="true"
                />
                <div className="container mx-auto px-4 py-12">
                    <div className="mx-auto max-w-3xl">
                        <Card className="border-rose-200/60 bg-white/85 shadow-2xl shadow-rose-500/10 backdrop-blur">
                            <CardHeader className="items-center gap-4 text-center">
                                <div className="relative flex h-20 w-20 items-center justify-center">
                                    <span className="absolute inset-0 animate-ping rounded-full bg-rose-400/25 motion-reduce:animate-none" />
                                    <span className="absolute inset-0 animate-pulse rounded-full bg-rose-500/10 motion-reduce:animate-none" />
                                    <span className="relative flex h-16 w-16 items-center justify-center rounded-full bg-rose-500 text-white shadow-lg shadow-rose-500/40">
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
                                            <path d="M18 6L6 18" />
                                            <path d="M6 6l12 12" />
                                        </svg>
                                    </span>
                                </div>
                                <div className="space-y-2">
                                    <CardTitle className="text-2xl text-rose-900">
                                        لم تكتمل عملية الدفع
                                    </CardTitle>
                                    <p className="text-sm text-muted-foreground">
                                        لا تقلق، يمكنك إعادة المحاولة الآن أو
                                        متابعة الطلب لاحقًا.
                                    </p>
                                </div>
                            </CardHeader>
                            <CardContent className="space-y-6">
                                <div className="rounded-2xl border border-rose-200/60 bg-rose-50/60 p-4 text-sm">
                                    <div className="flex flex-wrap items-center justify-between gap-2">
                                        <span className="text-muted-foreground">
                                            رقم الطلب
                                        </span>
                                        <span className="rounded-full bg-white px-3 py-1 font-semibold text-rose-900 shadow-sm">
                                            #{order.orderNumber}
                                        </span>
                                    </div>
                                </div>

                                {errors.payment && (
                                    <div className="rounded-xl border border-rose-200/70 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                                        {errors.payment}
                                    </div>
                                )}

                                <div className="flex flex-col gap-3 sm:flex-row sm:justify-center">
                                    <Button
                                        asChild
                                        className="sm:min-w-[160px]"
                                    >
                                        <Link
                                            href={
                                                paymentsRoutes.start(
                                                    order.orderNumber,
                                                ).url
                                            }
                                        >
                                            إعادة المحاولة
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="outline"
                                        asChild
                                        className="sm:min-w-[160px]"
                                    >
                                        <Link
                                            href={
                                                ordersRoutes.show(
                                                    order.orderNumber,
                                                ).url
                                            }
                                        >
                                            عرض الطلب
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
