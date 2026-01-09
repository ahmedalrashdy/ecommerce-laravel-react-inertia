import { Skeleton } from '@/components/ui/skeleton';
import { cn } from '@/lib/utils';
import * as React from 'react';

interface CartItemSkeletonProps {
    className?: string;
}

export const CartItemSkeleton: React.FC<CartItemSkeletonProps> = ({
    className,
}) => {
    return (
        <div
            className={cn('flex items-center gap-3 rounded-lg p-2', className)}
        >
            <Skeleton className="h-4 w-4 rounded" />
            <Skeleton className="h-20 w-20 flex-shrink-0 rounded-lg" />
            <div className="min-w-0 flex-1 space-y-2">
                <Skeleton className="h-4 w-32" />
                <Skeleton className="h-3 w-20" />
            </div>
            <div className="flex flex-col items-end gap-2">
                <Skeleton className="h-5 w-20" />
                <div className="flex items-center gap-2">
                    <Skeleton className="h-8 w-8 rounded" />
                    <Skeleton className="h-6 w-8" />
                    <Skeleton className="h-8 w-8 rounded" />
                </div>
            </div>
        </div>
    );
};
