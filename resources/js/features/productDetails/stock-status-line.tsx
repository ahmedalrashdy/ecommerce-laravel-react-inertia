import { AlertCircle, Check } from 'lucide-react';

export const StockStatusLine = ({ quantity }: { quantity: number }) => (
    <div className="text-xs font-medium">
        {quantity > 5 ? (
            <span className="flex items-center gap-1 text-green-600">
                <Check className="h-3 w-3" /> متوفر في المخزون
            </span>
        ) : quantity > 0 ? (
            <span className="flex items-center gap-1 text-orange-500">
                <AlertCircle className="h-3 w-3" /> متبقي {quantity} قطع فقط
            </span>
        ) : (
            <span className="text-red-500">غير متوفر حالياً</span>
        )}
    </div>
);
