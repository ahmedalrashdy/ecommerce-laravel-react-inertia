import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { OrderProgress } from '@/features/account/orders';
import AccountLayout from '@/features/account/layout/AccountLayout';
import { cn, storageUrl } from '@/lib/utils';
import ordersRoutes from '@/routes/store/account/orders';
import paymentsRoutes from '@/routes/store/payments';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import {
    ArrowLeft,
    CreditCard,
    MapPin,
    PackageCheck,
    Star,
    Truck,
} from 'lucide-react';
import type { ReactNode } from 'react';
import { useState } from 'react';

type OrderItem = {
    id: number;
    productId: number;
    name: string;
    image: string | null;
    attributes: Array<{
        name: string;
        value: string;
    }>;
    quantity: number;
    formattedPrice: string;
    formattedTotal: string;
    productSlug: string | null;
    review: {
        rating: number;
        comment: string | null;
    } | null;
};

type OrderDetailsProps = {
    order: {
        id: number;
        orderNumber: string;
        status: number;
        statusLabel: string;
        paymentStatus: number;
        paymentStatusLabel: string;
        paymentMethodLabel: string;
        trackingNumber: string | null;
        createdAt: string;
        expectedDelivery: string | null;
        shippingAddress: Record<string, string | null>;
        canPay: boolean;
        canReturn: boolean;
        canCancel: boolean;
        returnWindowEndsAt: string | null;
        canReview: boolean;
        items: OrderItem[];
    };
    summary: {
        formattedSubtotal: string;
        formattedTaxAmount: string;
        formattedShippingCost: string;
        formattedDiscountAmount: string;
        formattedGrandTotal: string;
    };
};

function OrderDetailsPage() {
    const { order, summary } = usePage<OrderDetailsProps>().props;
    const backTab =
        order.status === 4 || order.status === 5 || order.status === 6
            ? 'history'
            : 'active';
    const backUrl = ordersRoutes.index.url({ query: { tab: backTab } });
    const reviewForm = useForm<{
        product_id: number | null;
        rating: number;
        comment: string;
    }>({
        product_id: null,
        rating: 5,
        comment: '',
    });
    const [reviewDialogOpen, setReviewDialogOpen] = useState(false);
    const [reviewTarget, setReviewTarget] = useState<OrderItem | null>(null);

    const statusTone: Record<number, string> = {
        0: 'border-amber-200/60 bg-amber-50 text-amber-700 dark:border-amber-500/40 dark:bg-amber-500/10 dark:text-amber-200',
        1: 'border-sky-200/60 bg-sky-50 text-sky-700 dark:border-sky-500/40 dark:bg-sky-500/10 dark:text-sky-200',
        2: 'border-blue-200/60 bg-blue-50 text-blue-700 dark:border-blue-500/40 dark:bg-blue-500/10 dark:text-blue-200',
        3: 'border-indigo-200/60 bg-indigo-50 text-indigo-700 dark:border-indigo-500/40 dark:bg-indigo-500/10 dark:text-indigo-200',
        4: 'border-emerald-200/60 bg-emerald-50 text-emerald-700 dark:border-emerald-500/40 dark:bg-emerald-500/10 dark:text-emerald-200',
        5: 'border-rose-200/60 bg-rose-50 text-rose-700 dark:border-rose-500/40 dark:bg-rose-500/10 dark:text-rose-200',
        6: 'border-violet-200/60 bg-violet-50 text-violet-700 dark:border-violet-500/40 dark:bg-violet-500/10 dark:text-violet-200',
    };

    return (
        <>
            <Head title={`طلب ${order.orderNumber}`} />
            <div className="flex flex-col gap-8">
                <section className="rounded-3xl border border-border/60 bg-card p-6 shadow-sm">
                            <div className="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                                <div className="space-y-2">
                                    <p className="text-xs text-muted-foreground">
                                        تفاصيل الطلب
                                    </p>
                                    <div className="flex flex-wrap items-center gap-3">
                                        <h1 className="text-3xl font-semibold">
                                            #{order.orderNumber}
                                        </h1>
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
                                    </div>
                                    <p className="text-sm text-muted-foreground">
                                        تاريخ الطلب: {order.createdAt}
                                    </p>
                                </div>
                                <div className="flex flex-wrap gap-2">
                                    {order.canPay && (
                                        <Button asChild>
                                            <Link
                                                href={
                                                    paymentsRoutes.start(
                                                        order.id,
                                                    ).url
                                                }
                                            >
                                                إكمال الدفع
                                            </Link>
                                        </Button>
                                    )}
                                    {order.canReturn && (
                                        <Button
                                            size="sm"
                                            variant="outline"
                                            asChild
                                        >
                                            <Link
                                                href={
                                                    ordersRoutes.returns(
                                                        order.id,
                                                    ).url
                                                }
                                            >
                                                إنشاء إرجاع
                                            </Link>
                                        </Button>
                                    )}
                                    <Button
                                        asChild
                                        variant="outline"
                                    >
                                        <Link href={backUrl}>
                                            <ArrowLeft className="h-4 w-4" />
                                            العودة للطلبات
                                        </Link>
                                    </Button>
                                </div>
                            </div>
                            <div className="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                                <div className="rounded-2xl border border-border/60 bg-muted/40 px-4 py-3">
                                    <p className="text-xs text-muted-foreground">
                                        الإجمالي
                                    </p>
                                    <p className="text-lg font-semibold">
                                        {summary.formattedGrandTotal}
                                    </p>
                                </div>
                                <div className="rounded-2xl border border-border/60 bg-muted/40 px-4 py-3">
                                    <p className="text-xs text-muted-foreground">
                                        حالة الدفع
                                    </p>
                                    <p className="text-lg font-semibold">
                                        {order.paymentStatusLabel}
                                    </p>
                                </div>
                                <div className="rounded-2xl border border-border/60 bg-muted/40 px-4 py-3">
                                    <p className="text-xs text-muted-foreground">
                                        موعد الوصول المتوقع
                                    </p>
                                    <p className="text-lg font-semibold">
                                        {order.expectedDelivery ?? 'غير متوفر'}
                                    </p>
                                </div>
                                <div className="rounded-2xl border border-border/60 bg-muted/40 px-4 py-3">
                                    <p className="text-xs text-muted-foreground">
                                        عدد المنتجات
                                    </p>
                                    <p className="text-lg font-semibold">
                                        {order.items.length} عنصر
                                    </p>
                                </div>
                            </div>
                </section>

                <OrderProgress
                    status={order.status}
                    statusLabel={order.statusLabel}
                />

                <div className="grid gap-6 lg:grid-cols-[minmax(0,1fr)_340px]">
                    <section className="grid gap-4">
                                <div className="flex items-center justify-between">
                                    <h2 className="text-xl font-semibold">
                                        المنتجات
                                    </h2>
                                    <span className="text-sm text-muted-foreground">
                                        {order.items.length} عنصر
                                    </span>
                                </div>
                                <div className="grid gap-4">
                                    {order.items.map((item) => (
                                        <Card
                                            key={item.id}
                                            className="border-border/60 bg-card/80"
                                        >
                                            <CardContent className="grid gap-4 p-5 sm:grid-cols-[auto_1fr_auto] sm:items-center">
                                                <div className="relative h-20 w-20 overflow-hidden rounded-2xl border bg-muted">
                                                    {item.image ? (
                                                        <img
                                                            src={storageUrl(item.image)}
                                                            alt={item.name}
                                                            className="h-full w-full object-cover"
                                                        />
                                                    ) : (
                                                        <div className="flex h-full w-full items-center justify-center text-sm font-semibold text-muted-foreground">
                                                            {item.name.slice(
                                                                0,
                                                                2,
                                                            )}
                                                        </div>
                                                    )}
                                                    <span className="absolute right-2 bottom-2 rounded-full bg-background/90 px-2 py-0.5 text-xs font-semibold text-foreground shadow-sm">
                                                        ×{item.quantity}
                                                    </span>
                                                </div>
                                                <div className="grid gap-3">
                                                    <div className="flex flex-wrap items-center gap-2">
                                                        <h3 className="text-base font-semibold">
                                                            {item.name}
                                                        </h3>
                                                        {item.attributes
                                                            .length > 0 && (
                                                            <div className="flex flex-wrap gap-2 text-xs text-muted-foreground">
                                                                {item.attributes.map(
                                                                    (
                                                                        attribute,
                                                                    ) => (
                                                                        <span
                                                                            key={`${item.id}-${attribute.name}`}
                                                                            className="rounded-full border border-border/60 bg-muted/30 px-2 py-1"
                                                                        >
                                                                            {
                                                                                attribute.name
                                                                            }
                                                                            :{' '}
                                                                            {
                                                                                attribute.value
                                                                            }
                                                                        </span>
                                                                    ),
                                                                )}
                                                            </div>
                                                        )}
                                                    </div>
                                                    <div className="flex flex-wrap gap-4 text-sm text-muted-foreground">
                                                        <span>
                                                            السعر:{' '}
                                                            <span className="font-semibold text-foreground">
                                                                {
                                                                    item.formattedPrice
                                                                }
                                                            </span>
                                                        </span>
                                                        <span>
                                                            الإجمالي:{' '}
                                                            <span className="font-semibold text-foreground">
                                                                {
                                                                    item.formattedTotal
                                                                }
                                                            </span>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div className="flex flex-col gap-2">
                                                    <Button
                                                        size="sm"
                                                        variant="secondary"
                                                        disabled={
                                                            !order.canReview
                                                        }
                                                        onClick={() => {
                                                            setReviewTarget(
                                                                item,
                                                            );
                                                            reviewForm.setData(
                                                                'product_id',
                                                                item.productId,
                                                            );
                                                            reviewForm.setData(
                                                                'rating',
                                                                item.review
                                                                    ?.rating ??
                                                                    5,
                                                            );
                                                            reviewForm.setData(
                                                                'comment',
                                                                item.review
                                                                    ?.comment ??
                                                                    '',
                                                            );
                                                            setReviewDialogOpen(
                                                                true,
                                                            );
                                                        }}
                                                    >
                                                        {item.review
                                                            ? 'تحديث التقييم'
                                                            : 'كتابة تقييم'}
                                                    </Button>
                                                </div>
                                            </CardContent>
                                        </Card>
                                    ))}
                                </div>
                    </section>

                    <aside className="grid gap-4 lg:sticky lg:top-24">
                                <Card
                                    id="tracking"
                                    className="border-border/60 bg-card/80"
                                >
                                    <CardHeader>
                                        <CardTitle className="flex items-center gap-2 text-base">
                                            <Truck className="h-4 w-4" />
                                            معلومات الشحن
                                        </CardTitle>
                                    </CardHeader>
                                    <CardContent className="grid gap-3 text-sm text-muted-foreground">
                                        <div className="flex items-center justify-between">
                                            <span>موعد الوصول المتوقع</span>
                                            <span className="font-semibold text-foreground">
                                                {order.expectedDelivery ??
                                                    'غير متوفر'}
                                            </span>
                                        </div>
                                        <div className="flex items-center justify-between">
                                            <span>رقم التتبع</span>
                                            <span className="font-semibold text-foreground">
                                                {order.trackingNumber ?? '—'}
                                            </span>
                                        </div>
                                    </CardContent>
                                </Card>

                                <Card className="border-border/60 bg-card/80">
                                    <CardHeader>
                                        <CardTitle className="flex items-center gap-2 text-base">
                                            <PackageCheck className="h-4 w-4" />
                                            ملخص الدفع
                                        </CardTitle>
                                    </CardHeader>
                                    <CardContent className="grid gap-3 text-sm text-muted-foreground">
                                        <SummaryRow
                                            label="المجموع الفرعي"
                                            value={summary.formattedSubtotal}
                                        />
                                        <SummaryRow
                                            label="الضريبة"
                                            value={summary.formattedTaxAmount}
                                        />
                                        <SummaryRow
                                            label="الشحن"
                                            value={
                                                summary.formattedShippingCost
                                            }
                                        />
                                        <SummaryRow
                                            label="الخصم"
                                            value={
                                                summary.formattedDiscountAmount
                                            }
                                        />
                                        <div className="border-t pt-3">
                                            <SummaryRow
                                                label="الإجمالي"
                                                value={
                                                    summary.formattedGrandTotal
                                                }
                                                emphasized
                                            />
                                        </div>
                                    </CardContent>
                                </Card>

                                <Card className="border-border/60 bg-card/80">
                                    <CardHeader>
                                        <CardTitle className="flex items-center gap-2 text-base">
                                            <CreditCard className="h-4 w-4" />
                                            طريقة الدفع
                                        </CardTitle>
                                    </CardHeader>
                                    <CardContent className="grid gap-3 text-sm text-muted-foreground">
                                        <SummaryRow
                                            label="الطريقة"
                                            value={order.paymentMethodLabel}
                                        />
                                        <SummaryRow
                                            label="حالة الدفع"
                                            value={order.paymentStatusLabel}
                                        />
                                    </CardContent>
                                </Card>

                                <Card className="border-border/60 bg-card/80">
                                    <CardHeader>
                                        <CardTitle className="flex items-center gap-2 text-base">
                                            <MapPin className="h-4 w-4" />
                                            عنوان الشحن
                                        </CardTitle>
                                    </CardHeader>
                                    <CardContent className="grid gap-2 text-sm text-muted-foreground">
                                        <p className="font-semibold text-foreground">
                                            {order.shippingAddress
                                                .contact_person ?? '—'}
                                        </p>
                                        <p>
                                            {order.shippingAddress
                                                .contact_phone ?? '—'}
                                        </p>
                                        <p>
                                            {[
                                                order.shippingAddress
                                                    .address_line_1,
                                                order.shippingAddress
                                                    .address_line_2,
                                                order.shippingAddress.city,
                                                order.shippingAddress.state,
                                            ]
                                                .filter(Boolean)
                                                .join('، ') || '—'}
                                        </p>
                                        <p>
                                            {[
                                                order.shippingAddress.country,
                                                order.shippingAddress
                                                    .postal_code,
                                            ]
                                                .filter(Boolean)
                                                .join(' - ') || '—'}
                                        </p>
                                    </CardContent>
                                </Card>
                    </aside>
                </div>
            </div>
            <Dialog
                open={reviewDialogOpen}
                onOpenChange={(open) => {
                    if (!open) {
                        setReviewDialogOpen(false);
                        reviewForm.reset();
                    }
                }}
            >
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>
                            {reviewTarget?.review
                                ? 'تحديث التقييم'
                                : 'كتابة تقييم'}
                        </DialogTitle>
                        <DialogDescription>
                            شاركنا رأيك في المنتج، تقييمك يساعد العملاء الآخرين.
                        </DialogDescription>
                    </DialogHeader>
                    <div className="space-y-4">
                        <div className="space-y-2">
                            <Label>التقييم</Label>
                            <div className="flex items-center gap-2">
                                {Array.from({ length: 5 }, (_, index) => {
                                    const value = index + 1;
                                    const isActive =
                                        reviewForm.data.rating >= value;

                                    return (
                                        <button
                                            key={value}
                                            type="button"
                                            onClick={() =>
                                                reviewForm.setData(
                                                    'rating',
                                                    value,
                                                )
                                            }
                                            className={cn(
                                                'flex h-10 w-10 items-center justify-center rounded-full border text-sm transition',
                                                isActive
                                                    ? 'border-amber-300 bg-amber-100 text-amber-600'
                                                    : 'border-muted-foreground/30 text-muted-foreground hover:border-amber-200 hover:text-amber-500',
                                            )}
                                        >
                                            <Star className="h-4 w-4" />
                                        </button>
                                    );
                                })}
                            </div>
                            {reviewForm.errors.rating && (
                                <p className="text-xs text-destructive">
                                    {reviewForm.errors.rating}
                                </p>
                            )}
                        </div>
                        <div className="space-y-2">
                            <Label>ملاحظاتك</Label>
                            <textarea
                                className="min-h-[120px] w-full rounded-md border bg-background px-3 py-2 text-sm shadow-xs focus-visible:ring-2 focus-visible:ring-ring/50 focus-visible:outline-none"
                                value={reviewForm.data.comment}
                                onChange={(event) =>
                                    reviewForm.setData(
                                        'comment',
                                        event.target.value,
                                    )
                                }
                                placeholder="اكتب تجربتك مع المنتج"
                            />
                            {reviewForm.errors.comment && (
                                <p className="text-xs text-destructive">
                                    {reviewForm.errors.comment}
                                </p>
                            )}
                        </div>
                    </div>
                    <DialogFooter>
                        <Button
                            variant="outline"
                            onClick={() => setReviewDialogOpen(false)}
                        >
                            إغلاق
                        </Button>
                        <Button
                            onClick={() =>
                                reviewForm.post(
                                    ordersRoutes.reviews(order.id).url,
                                    {
                                        preserveScroll: true,
                                        onSuccess: () => {
                                            setReviewDialogOpen(false);
                                        },
                                    },
                                )
                            }
                            disabled={
                                reviewForm.processing ||
                                !reviewForm.data.product_id ||
                                reviewForm.data.rating < 1
                            }
                        >
                            حفظ التقييم
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </>
    );
}

OrderDetailsPage.layout = (page: ReactNode) => (
    <AccountLayout>{page}</AccountLayout>
);

export default OrderDetailsPage;

function SummaryRow({
    label,
    value,
    emphasized = false,
}: {
    label: string;
    value: string;
    emphasized?: boolean;
}) {
    return (
        <div
            className={cn(
                'flex items-center justify-between',
                emphasized && 'text-base font-semibold text-foreground',
            )}
        >
            <span>{label}</span>
            <span className={cn(emphasized && 'text-lg')}>{value}</span>
        </div>
    );
}
