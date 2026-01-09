import { Skeleton } from '@/components/ui/skeleton';
import { cn } from '@/lib/utils';
import * as React from 'react';

interface CartDropdownSkeletonProps {
    className?: string;
}

export const CartDropdownSkeleton: React.FC<CartDropdownSkeletonProps> = ({
    className,
}) => {
    return (
        <div className={cn('space-y-2 p-2', className)}>
            {[1, 2, 3].map((i) => (
                <div
                    key={i}
                    className="flex items-center gap-3 rounded-lg p-2"
                >
                    <Skeleton className="h-16 w-16 flex-shrink-0 rounded-lg" />
                    <div className="min-w-0 flex-1 space-y-2">
                        <Skeleton className="h-4 w-32" />
                        <Skeleton className="h-3 w-20" />
                    </div>
                    <Skeleton className="h-4 w-4 rounded" />
                </div>
            ))}
        </div>
    );
};
