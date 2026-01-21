import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { cn, storageUrl } from '@/lib/utils';
import { PackageCheck } from 'lucide-react';

export type ReturnType = 'full' | 'partial';

export type ReturnDraftItem = {
    id: number;
    name: string;
    image: string | null;
    attributes: Array<{ name: string; value: string }>;
    maxQuantity: number;
    quantity: number;
    reason: string;
    selected: boolean;
};

export function ReturnTypeSelector({
    value,
    onChange,
}: {
    value: ReturnType;
    onChange: (value: ReturnType) => void;
}) {
    const options: Array<{
        value: ReturnType;
        title: string;
        description: string;
    }> = [
        {
            value: 'full',
            title: 'إرجاع كلي',
            description: 'يرجع كامل الطلب مع سبب واحد واضح.',
        },
        {
            value: 'partial',
            title: 'إرجاع جزئي',
            description: 'اختر عناصر محددة وحدد سببًا لكل عنصر.',
        },
    ];

    return (
        <div className="grid gap-3 md:grid-cols-2">
            {options.map((option) => {
                const isActive = value === option.value;

                return (
                    <button
                        key={option.value}
                        type="button"
                        onClick={() => onChange(option.value)}
                        className={cn(
                            'flex flex-col items-start gap-2 rounded-2xl border px-4 py-4 text-right transition',
                            isActive
                                ? 'border-primary/40 bg-primary/5 shadow-sm'
                                : 'border-border/60 hover:border-primary/30',
                        )}
                    >
                        <span className="text-sm font-semibold">
                            {option.title}
                        </span>
                        <span className="text-xs text-muted-foreground">
                            {option.description}
                        </span>
                    </button>
                );
            })}
        </div>
    );
}

export function ReturnReasonField({
    value,
    onChange,
    error,
    label = 'سبب الإرجاع',
    helper,
}: {
    value: string;
    onChange: (value: string) => void;
    error?: string;
    label?: string;
    helper?: string;
}) {
    return (
        <div className="space-y-2">
            <Label>{label}</Label>
            <textarea
                className="min-h-[120px] w-full rounded-md border bg-background px-3 py-2 text-sm shadow-xs focus-visible:ring-2 focus-visible:ring-ring/50 focus-visible:outline-none"
                value={value}
                onChange={(event) => onChange(event.target.value)}
                placeholder="اكتب سبب الإرجاع"
            />
            {helper && (
                <p className="text-xs text-muted-foreground">{helper}</p>
            )}
            {error && <p className="text-xs text-destructive">{error}</p>}
        </div>
    );
}

export function ReturnItemsCard({
    items,
    onUpdate,
    onToggleAll,
}: {
    items: ReturnDraftItem[];
    onUpdate: (id: number, updates: Partial<ReturnDraftItem>) => void;
    onToggleAll: (nextState: boolean) => void;
}) {
    const shouldSelectAll = items.some((item) => !item.selected);

    return (
        <Card>
            <CardHeader className="flex flex-row items-center justify-between">
                <CardTitle className="flex items-center gap-2 text-base">
                    <PackageCheck className="h-4 w-4" />
                    عناصر الإرجاع الجزئي
                </CardTitle>
                <Button
                    type="button"
                    size="sm"
                    variant="outline"
                    onClick={() => onToggleAll(shouldSelectAll)}
                >
                    {shouldSelectAll ? 'تحديد الكل' : 'إلغاء التحديد'}
                </Button>
            </CardHeader>
            <CardContent className="space-y-4">
                {items.map((item) => (
                    <div key={item.id} className="rounded-2xl border p-4">
                        <div className="flex flex-wrap items-start justify-between gap-4">
                            <div className="flex flex-1 flex-wrap items-center gap-4">
                                <div className="h-16 w-16 overflow-hidden rounded-2xl border bg-muted">
                                    {item.image ? (
                                        <img
                                            src={storageUrl(item.image)}
                                            alt={item.name}
                                            className="h-full w-full object-cover"
                                        />
                                    ) : (
                                        <div className="flex h-full w-full items-center justify-center text-sm font-semibold text-muted-foreground">
                                            {item.name.slice(0, 2)}
                                        </div>
                                    )}
                                </div>
                                <div className="space-y-2">
                                    <div className="flex items-center gap-2">
                                        <input
                                            type="checkbox"
                                            checked={item.selected}
                                            onChange={(event) =>
                                                onUpdate(item.id, {
                                                    selected:
                                                        event.target.checked,
                                                    quantity:
                                                        event.target.checked
                                                            ? Math.max(
                                                                  1,
                                                                  item.quantity ||
                                                                      1,
                                                              )
                                                            : 0,
                                                })
                                            }
                                            className="h-4 w-4 rounded border"
                                        />
                                        <span className="font-semibold">
                                            {item.name}
                                        </span>
                                    </div>
                                    {item.attributes.length > 0 && (
                                        <div className="flex flex-wrap gap-2 text-xs text-muted-foreground">
                                            {item.attributes.map(
                                                (attribute) => (
                                                    <span
                                                        key={`${item.id}-${attribute.name}`}
                                                        className="rounded-full border px-2 py-1"
                                                    >
                                                        {attribute.name}:{' '}
                                                        {attribute.value}
                                                    </span>
                                                ),
                                            )}
                                        </div>
                                    )}
                                    <p className="text-xs text-muted-foreground">
                                        الكمية المشتراة: {item.maxQuantity}
                                    </p>
                                </div>
                            </div>
                            <div className="flex flex-wrap items-center gap-3">
                                <Label className="text-xs text-muted-foreground">
                                    الكمية المرتجعة
                                </Label>
                                <input
                                    type="number"
                                    min={1}
                                    max={item.maxQuantity}
                                    value={item.quantity}
                                    disabled={!item.selected}
                                    onChange={(event) => {
                                        const value = Math.max(
                                            1,
                                            Math.min(
                                                item.maxQuantity,
                                                Number(event.target.value) || 1,
                                            ),
                                        );
                                        onUpdate(item.id, {
                                            quantity: value,
                                        });
                                    }}
                                    className="h-9 w-20 rounded-md border bg-background px-2 text-center text-sm"
                                />
                            </div>
                        </div>
                        <div className="mt-4 space-y-2">
                            <Label>سبب الإرجاع</Label>
                            <textarea
                                className={cn(
                                    'min-h-[80px] w-full rounded-md border bg-background px-3 py-2 text-sm shadow-xs focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring/50',
                                    !item.selected && 'opacity-60',
                                )}
                                value={item.reason}
                                disabled={!item.selected}
                                onChange={(event) =>
                                    onUpdate(item.id, {
                                        reason: event.target.value,
                                    })
                                }
                                placeholder="مثال: المقاس غير مناسب"
                            />
                        </div>
                    </div>
                ))}
            </CardContent>
        </Card>
    );
}
