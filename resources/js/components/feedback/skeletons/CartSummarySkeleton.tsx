import { Skeleton } from '@/components/ui/skeleton';
import { cn } from '@/lib/utils';
import * as React from 'react';

interface CartSummarySkeletonProps {
    className?: string;
}

export const CartSummarySkeleton: React.FC<CartSummarySkeletonProps> = ({
    className,
}) => {
    return (
        <div className={cn('space-y-4 rounded-lg border p-6', className)}>
            <Skeleton className="h-6 w-32" />
            <div className="flex items-center gap-2">
                <Skeleton className="h-5 w-5 rounded" />
                <Skeleton className="h-4 w-24" />
            </div>
            <div className="space-y-3 border-t pt-4">
                <div className="flex justify-between">
                    <Skeleton className="h-4 w-24" />
                    <Skeleton className="h-4 w-20" />
                </div>
                <div className="flex justify-between">
                    <Skeleton className="h-5 w-20" />
                    <Skeleton className="h-5 w-24" />
                </div>
            </div>
            <Skeleton className="h-11 w-full rounded-lg" />
        </div>
    );
};
