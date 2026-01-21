import { Badge } from '@/components/ui/badge';

export function InspectionDetails({
    inspections,
}: {
    inspections: App.Data.Basic.ReturnInspectionData[];
}) {
    if (inspections.length === 0) {
        return (
            <div className="rounded-lg border border-dashed bg-muted/20 px-3 py-2 text-xs text-muted-foreground">
                لم يتم فحص هذا العنصر بعد.
            </div>
        );
    }

    return (
        <div className="space-y-2">
            {inspections.map((inspection, index) => (
                <div
                    key={`${inspection.conditionLabel}-${index}`}
                    className="rounded-lg border bg-muted/40 px-3 py-2"
                >
                    <div className="flex flex-wrap items-center gap-2 text-xs text-muted-foreground">
                        <Badge variant="secondary">{inspection.conditionLabel}</Badge>
                        <Badge variant="outline">{inspection.resolutionLabel}</Badge>
                        <span>الكمية: {inspection.quantity}</span>
                        {inspection.formattedRefundAmount && (
                            <span>قيمة الاسترجاع: {inspection.formattedRefundAmount}</span>
                        )}
                    </div>
                    {inspection.note && (
                        <p className="mt-2 text-xs text-muted-foreground">
                            ملاحظة الفحص: {inspection.note}
                        </p>
                    )}
                </div>
            ))}
        </div>
    );
}
