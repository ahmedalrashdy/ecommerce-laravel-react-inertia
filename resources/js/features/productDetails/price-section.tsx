import { Badge } from '@/components/ui/badge';

export const PriceSection = ({
    price,
    compareAtPrice,
    discountPercent,
}: {
    price: string;
    compareAtPrice: string | null;
    discountPercent: number | null;
}) => (
    <div className="flex flex-wrap items-center justify-between gap-4 rounded-lg border bg-card p-4 shadow-sm">
        <div>
            <div className="flex items-baseline gap-2">
                <span className="text-3xl font-bold text-primary">{price}</span>
                {compareAtPrice && (
                    <span className="text-sm text-muted-foreground line-through decoration-red-500/50">
                        {compareAtPrice}
                    </span>
                )}
            </div>
            <p className="mt-1 text-[10px] text-muted-foreground">
                السعر شامل ضريبة القيمة المضافة
            </p>
        </div>
        {discountPercent && discountPercent > 0 && (
            <Badge
                variant="destructive"
                className="h-fit px-2 py-1 text-xs"
            >
                خصم {discountPercent}%
            </Badge>
        )}
    </div>
);
