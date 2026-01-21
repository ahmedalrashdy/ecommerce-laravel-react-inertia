import { AddressForm } from '@/features/account/addresses/AddressForm';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import StoreLayout from '@/layouts/StoreLayout';
import { formatCurrency, storageUrl } from '@/lib/utils';
import addressesRoutes from '@/routes/store/account/addresses';
import checkoutRoutes from '@/routes/store/checkout';
import type { UserAddress } from '@/types/address';
import { Form, Head, usePage } from '@inertiajs/react';
import { useMemo, useState } from 'react';

type CheckoutSummary = {
    subtotal: string;
    taxAmount: string;
    shippingCost: string;
    discountAmount: string;
    grandTotal: string;
    formattedSubtotal: string;
    formattedTaxAmount: string;
    formattedShippingCost: string;
    formattedDiscountAmount: string;
    formattedGrandTotal: string;
};

type CheckoutPageProps = {
    items: App.Data.Basic.CartItemData[];
    addresses: UserAddress[];
    defaultShippingAddressId: number | null;
    idempotencyKey: string;
    summary: CheckoutSummary;
};

export default function CheckoutPage() {
    const {
        items,
        addresses,
        defaultShippingAddressId,
        idempotencyKey,
        summary,
    } = usePage<CheckoutPageProps>().props;

    const firstAddressId = addresses[0]?.id ?? null;
    const initialShippingId = defaultShippingAddressId ?? firstAddressId;

    const [shippingAddressId, setShippingAddressId] = useState<number | null>(
        initialShippingId,
    );

    const selectedVariantIds = useMemo(
        () => items.map((item) => item.productVariant.id),
        [items],
    );

    const shippingAddress = useMemo(
        () => addresses.find((address) => address.id === shippingAddressId),
        [addresses, shippingAddressId],
    );

    if (addresses.length === 0) {
        return (
            <StoreLayout>
                <Head title="إتمام الطلب" />
                <div className="container mx-auto px-4 py-8">
                    <div className="mx-auto max-w-4xl">
                        <Card>
                            <CardHeader>
                                <CardTitle>أضف عنوان الشحن أولاً</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-6">
                                <p className="text-sm text-muted-foreground">
                                    لا يمكنك إتمام الطلب بدون عنوان شحن.
                                </p>
                                <AddressForm
                                    action={addressesRoutes.store.url({
                                        query: {
                                            redirect_to:
                                                checkoutRoutes.index().url,
                                        },
                                    })}
                                    method="post"
                                />
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </StoreLayout>
        );
    }

    return (
        <StoreLayout>
            <Head title="إتمام الطلب" />
            <div className="container mx-auto px-4 py-8">
                <div className="flex flex-col gap-6">
                    <h1 className="text-2xl font-bold">إتمام الطلب</h1>
                    <Form
                        action={checkoutRoutes.placeOrder().url}
                        method="post"
                        headers={{ 'X-Idempotency-Key': idempotencyKey }}
                        className="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]"
                    >
                        {({ processing, errors }) => (
                            <>
                                <div className="space-y-6">
                                    <Card>
                                        <CardHeader>
                                            <CardTitle>المنتجات</CardTitle>
                                        </CardHeader>
                                        <CardContent className="space-y-4">
                                            {items.map((item) => (
                                                <div
                                                    key={item.id}
                                                    className="flex items-center justify-between gap-4 border-b pb-4 last:border-b-0 last:pb-0"
                                                >
                                                    <div className="flex items-center gap-4">
                                                        <img
                                                            src={storageUrl(
                                                                item
                                                                    .productVariant
                                                                    .defaultImage
                                                                    ?.path,
                                                            )}
                                                            alt={
                                                                item.product
                                                                    .name
                                                            }
                                                            className="h-16 w-16 rounded-md border object-cover"
                                                        />
                                                        <div className="space-y-1">
                                                            <p className="font-medium">
                                                                {
                                                                    item.product
                                                                        .name
                                                                }
                                                            </p>
                                                            <p className="text-sm text-muted-foreground">
                                                                الكمية:{' '}
                                                                {item.quantity}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <p className="text-sm font-semibold">
                                                        {formatCurrency(
                                                            item.productVariant
                                                                .price,
                                                        )}
                                                    </p>
                                                </div>
                                            ))}
                                        </CardContent>
                                    </Card>

                                    <Card>
                                        <CardHeader>
                                            <CardTitle>عنوان الشحن</CardTitle>
                                        </CardHeader>
                                        <CardContent className="space-y-4">
                                            <div className="grid gap-2">
                                                <Label>اختر عنوان الشحن</Label>
                                                <Select
                                                    value={
                                                        shippingAddressId?.toString() ??
                                                        ''
                                                    }
                                                    onValueChange={(value) =>
                                                        setShippingAddressId(
                                                            Number(value),
                                                        )
                                                    }
                                                >
                                                    <SelectTrigger>
                                                        <SelectValue placeholder="اختر عنواناً" />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        {addresses.map(
                                                            (address) => (
                                                                <SelectItem
                                                                    key={
                                                                        address.id
                                                                    }
                                                                    value={address.id.toString()}
                                                                >
                                                                    {
                                                                        address.contact_person
                                                                    }{' '}
                                                                    -{' '}
                                                                    {
                                                                        address.address_line_1
                                                                    }
                                                                </SelectItem>
                                                            ),
                                                        )}
                                                    </SelectContent>
                                                </Select>
                                                <InputError
                                                    message={
                                                        errors.shipping_address_id
                                                    }
                                                />
                                            </div>

                                            {shippingAddress && (
                                                <AddressPreview
                                                    address={shippingAddress}
                                                />
                                            )}
                                        </CardContent>
                                    </Card>

                                    <Card>
                                        <CardHeader>
                                            <CardTitle>
                                                ملاحظات إضافية
                                            </CardTitle>
                                        </CardHeader>
                                        <CardContent className="grid gap-2">
                                            <textarea
                                                name="notes"
                                                rows={3}
                                                className="w-full rounded-md border px-3 py-2 text-sm focus:outline-none"
                                                placeholder="أي ملاحظات خاصة بالطلب"
                                            />
                                            <InputError
                                                message={errors.notes}
                                            />
                                        </CardContent>
                                    </Card>
                                </div>

                                <div className="space-y-6">
                                    <Card>
                                        <CardHeader>
                                            <CardTitle>ملخص الطلب</CardTitle>
                                        </CardHeader>
                                        <CardContent className="space-y-4">
                                            <SummaryRow
                                                label="المجموع الفرعي"
                                                value={
                                                    summary.formattedSubtotal
                                                }
                                            />
                                            <SummaryRow
                                                label="الشحن"
                                                value={
                                                    summary.formattedShippingCost
                                                }
                                            />
                                            <SummaryRow
                                                label="الضريبة"
                                                value={
                                                    summary.formattedTaxAmount
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
                                                    bold
                                                />
                                            </div>
                                        </CardContent>
                                    </Card>

                                    <Button
                                        type="submit"
                                        disabled={
                                            processing || !shippingAddressId
                                        }
                                        className="w-full"
                                    >
                                        {processing
                                            ? 'جارٍ تحويلك للدفع...'
                                            : 'ادفع الآن'}
                                    </Button>
                                </div>

                                <input
                                    type="hidden"
                                    name="shipping_address_id"
                                    value={shippingAddressId ?? ''}
                                />
                                {selectedVariantIds.map((variantId) => (
                                    <input
                                        key={variantId}
                                        type="hidden"
                                        name="selected_items[]"
                                        value={variantId}
                                    />
                                ))}
                                <InputError message={errors.selected_items} />
                            </>
                        )}
                    </Form>
                </div>
            </div>
        </StoreLayout>
    );
}

function AddressPreview({ address }: { address: UserAddress }) {
    return (
        <div className="rounded-md border bg-muted/40 p-3 text-sm">
            <p className="font-medium">{address.contact_person}</p>
            <p className="text-muted-foreground">{address.contact_phone}</p>
            <p className="text-muted-foreground">{address.address_line_1}</p>
            <p className="text-muted-foreground">
                {[address.city, address.state, address.country]
                    .filter(Boolean)
                    .join(' - ')}
            </p>
        </div>
    );
}

function SummaryRow({
    label,
    value,
    bold = false,
}: {
    label: string;
    value: string;
    bold?: boolean;
}) {
    return (
        <div className="flex items-center justify-between text-sm">
            <span className={bold ? 'font-semibold' : 'text-muted-foreground'}>
                {label}
            </span>
            <span className={bold ? 'font-semibold' : ''}>{value}</span>
        </div>
    );
}
