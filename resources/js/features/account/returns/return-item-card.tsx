import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { InspectionDetails } from '@/features/account/returns/inspection-details';
import { storageUrl } from '@/lib/utils';

export function ReturnItemCard({
    item,
}: {
    item: App.Data.Basic.ReturnItemDetailsData;
}) {
    return (
        <Card className="border-border/60">
            <CardContent className="p-5">
                <div className="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div className="flex flex-1 flex-wrap items-start gap-4">
                        <div className="h-20 w-20 overflow-hidden rounded-2xl border bg-muted">
                            {item.image ? (
                                <img
                                    src={storageUrl(item.image)}
                                    alt={item.productName}
                                    className="h-full w-full object-cover"
                                       loading="lazy"
                    decoding="async"
                                />
                            ) : (
                                <div className="flex h-full w-full items-center justify-center text-sm font-semibold text-muted-foreground">
                                    {item.productName.slice(0, 2)}
                                </div>
                            )}
                        </div>
                        <div className="space-y-2">
                            <div className="flex flex-wrap items-center gap-2">
                                <h3 className="text-base font-semibold">
                                    {item.productName}
                                </h3>
                                <Badge variant="outline">
                                    {item.inspectionStatus}
                                </Badge>
                            </div>
                            {item.attributes.length > 0 && (
                                <div className="flex flex-wrap gap-2 text-xs text-muted-foreground">
                                    {item.attributes.map((attribute) => (
                                        <span
                                            key={`${item.id}-${attribute.name}`}
                                            className="rounded-full border px-2 py-1"
                                        >
                                            {attribute.name}: {attribute.value}
                                        </span>
                                    ))}
                                </div>
                            )}
                            <p className="text-sm text-muted-foreground">
                                سبب الإرجاع:{' '}
                                <span className="font-semibold text-foreground">
                                    {item.reason}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div className="flex flex-col gap-2 text-sm text-muted-foreground">
                        <span>
                            الكمية:{' '}
                            <span className="font-semibold text-foreground">
                                {item.quantity}
                            </span>
                        </span>
                        <span>
                            سعر الوحدة:{' '}
                            <span className="font-semibold text-foreground">
                                {item.formattedUnitPrice}
                            </span>
                        </span>
                        <span>
                            الإجمالي:{' '}
                            <span className="font-semibold text-foreground">
                                {item.formattedTotal}
                            </span>
                        </span>
                    </div>
                </div>
                <div className="mt-4 space-y-2">
                    <p className="text-sm font-semibold">تفاصيل الفحص</p>
                    <InspectionDetails inspections={item.inspections} />
                </div>
            </CardContent>
        </Card>
    );
}
