import { Skeleton } from '@/components/ui/skeleton';
import { cn } from '@/lib/utils';
import * as React from 'react';

interface CartPageSkeletonProps {
    className?: string;
}

export const CartPageSkeleton: React.FC<CartPageSkeletonProps> = ({
    className,
}) => {
    return (
        <div className={cn('space-y-4', className)}>
            {/* Header */}
            <div className="flex items-center justify-between">
                <div className="space-y-2">
                    <Skeleton className="h-8 w-32" />
                    <Skeleton className="h-4 w-24" />
                </div>
                <Skeleton className="h-6 w-20" />
            </div>

            {/* Items */}
            <div className="space-y-4">
                {[1, 2, 3].map((i) => (
                    <div
                        key={i}
                        className="flex items-center gap-3 rounded-lg border p-4"
                    >
                        <Skeleton className="h-4 w-4 rounded" />
                        <Skeleton className="h-24 w-24 flex-shrink-0 rounded-lg" />
                        <div className="min-w-0 flex-1 space-y-2">
                            <Skeleton className="h-5 w-48" />
                            <Skeleton className="h-4 w-24" />
                        </div>
                        <div className="flex flex-col items-end gap-2">
                            <Skeleton className="h-6 w-24" />
                            <div className="flex items-center gap-2">
                                <Skeleton className="h-9 w-9 rounded" />
                                <Skeleton className="h-6 w-8" />
                                <Skeleton className="h-9 w-9 rounded" />
                            </div>
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
};
