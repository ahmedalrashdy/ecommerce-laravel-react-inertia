import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import AccountLayout from '@/features/account/layout/AccountLayout';
import ordersRoutes from '@/routes/store/account/orders';
import {
    ReturnItemsCard,
    ReturnReasonField,
    ReturnTypeSelector,
    type ReturnDraftItem,
    type ReturnType,
} from '@/features/account/orders/return-form';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import type { ReactNode } from 'react';
import { useMemo, useState } from 'react';

type ReturnItem = {
    id: number;
    productId: number;
    name: string;
    image: string | null;
    attributes: Array<{ name: string; value: string }>;
    quantity: number;
};

type ReturnPageProps = {
    order: {
        id: number;
        orderNumber: string;
        returnWindowEndsAt: string | null;
    };
    items: ReturnItem[];
};

type ReturnFormPayload = {
    return_type: ReturnType;
    reason: string;
    items: Array<{ order_item_id: number; quantity: number; reason: string }>;
};

function OrderReturnPage() {
    const { order, items } = usePage<ReturnPageProps>().props;
    const [returnType, setReturnType] = useState<ReturnType>('partial');
    const [draftItems, setDraftItems] = useState<ReturnDraftItem[]>(
        items.map((item) => ({
            id: item.id,
            name: item.name,
            image: item.image,
            attributes: item.attributes,
            maxQuantity: item.quantity,
            quantity: item.quantity,
            reason: '',
            selected: true,
        })),
    );

    const form = useForm<ReturnFormPayload>({
        return_type: returnType,
        reason: '',
        items: [],
    });

    const updateDraft = (id: number, updates: Partial<ReturnDraftItem>) => {
        setDraftItems((prev) =>
            prev.map((item) =>
                item.id === id ? { ...item, ...updates } : item,
            ),
        );
    };

    const handleReturnTypeChange = (nextType: ReturnType) => {
        setReturnType(nextType);
        form.setData('return_type', nextType);

        if (nextType === 'full') {
            setDraftItems((prev) =>
                prev.map((item) => ({
                    ...item,
                    selected: false,
                    quantity: 0,
                })),
            );
        }
    };

    const handleToggleAll = (nextState: boolean) => {
        setDraftItems((prev) =>
            prev.map((item) => ({
                ...item,
                selected: nextState,
                quantity: nextState ? Math.max(1, item.quantity || 1) : 0,
            })),
        );
    };

    const returnItemsPayload = useMemo(
        () =>
            draftItems
                .filter((item) => item.selected)
                .map((item) => ({
                    order_item_id: item.id,
                    quantity: item.quantity,
                    reason: item.reason.trim(),
                })),
        [draftItems],
    );

    const isSubmitDisabled =
        form.processing ||
        (returnType === 'full'
            ? !form.data.reason.trim()
            : returnItemsPayload.length === 0 ||
              returnItemsPayload.some(
                  (item) => item.quantity < 1 || !item.reason,
              ));

    return (
        <>
            <Head title={`إرجاع الطلب ${order.orderNumber}`} />
            <div className="flex flex-col gap-6">
                <header className="flex flex-wrap items-center justify-between gap-4">
                    <div className="space-y-2">
                        <p className="text-sm text-muted-foreground">
                            إرجاع الطلب
                        </p>
                        <h1 className="text-3xl font-semibold">
                            #{order.orderNumber}
                        </h1>
                        <p className="text-sm text-muted-foreground">
                            نافذة الاسترجاع:{' '}
                            <span className="font-semibold text-foreground">
                                {order.returnWindowEndsAt ?? '—'}
                            </span>
                        </p>
                    </div>
                    <Button
                        asChild
                        variant="outline"
                    >
                        <Link href={ordersRoutes.show(order.id).url}>
                            <ArrowLeft className="h-4 w-4" />
                            الرجوع للطلب
                        </Link>
                    </Button>
                </header>

                <Card className="p-6">
                    <div className="space-y-4">
                        <div className="space-y-2">
                            <h2 className="text-lg font-semibold">
                                نوع الإرجاع
                            </h2>
                            <p className="text-sm text-muted-foreground">
                                اختر ما إذا كنت تريد إرجاع الطلب بالكامل أو
                                إرجاع عناصر محددة فقط.
                            </p>
                        </div>
                        <ReturnTypeSelector
                            value={returnType}
                            onChange={handleReturnTypeChange}
                        />
                    </div>
                </Card>

                {returnType === 'full' ? (
                    <Card className="p-6">
                        <ReturnReasonField
                            value={form.data.reason}
                            onChange={(value) =>
                                form.setData('reason', value)
                            }
                            error={form.errors.reason}
                            helper="يجب كتابة سبب واضح قبل إرسال طلب الإرجاع الكامل."
                        />
                    </Card>
                ) : (
                    <ReturnItemsCard
                        items={draftItems}
                        onUpdate={updateDraft}
                        onToggleAll={handleToggleAll}
                    />
                )}

                {form.errors.items && (
                    <p className="text-xs text-destructive">
                        {form.errors.items}
                    </p>
                )}

                <div className="flex flex-wrap items-center justify-between gap-4">
                    <p className="text-sm text-muted-foreground">
                        {returnType === 'full'
                            ? 'سيتم إرجاع كامل الطلب عند الموافقة.'
                            : `العناصر المحددة: ${returnItemsPayload.length}`}
                    </p>
                    <Button
                        disabled={isSubmitDisabled}
                        onClick={() => {
                            form.setData('return_type', returnType);
                            form.setData(
                                'items',
                                returnType === 'partial'
                                    ? returnItemsPayload
                                    : [],
                            );
                            form.post(ordersRoutes.returns(order.id).url, {
                                preserveScroll: true,
                            });
                        }}
                    >
                        إرسال طلب الاسترجاع
                    </Button>
                </div>
            </div>
        </>
    );
}

OrderReturnPage.layout = (page: ReactNode) => (
    <AccountLayout>{page}</AccountLayout>
);

export default OrderReturnPage;
