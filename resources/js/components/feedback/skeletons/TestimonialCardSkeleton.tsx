import { Skeleton } from '@/components/ui/skeleton';
import { cn } from '@/lib/utils';
import * as React from 'react';

interface TestimonialCardSkeletonProps {
    className?: string;
}

export const TestimonialCardSkeleton: React.FC<
    TestimonialCardSkeletonProps
> = ({ className }) => {
    return (
        <div
            className={cn(
                'h-full rounded-2xl border border-border bg-card p-6 shadow-md',
                className,
            )}
        >
            {/* Quote Icon */}
            <div className="mb-4">
                <Skeleton className="h-10 w-10 rounded" />
            </div>

            {/* Rating */}
            <div className="mb-4 flex items-center gap-1">
                {Array.from({ length: 5 }).map((_, index) => (
                    <Skeleton
                        key={index}
                        className="h-5 w-5 rounded"
                    />
                ))}
            </div>

            {/* Comment */}
            <div className="mb-6 space-y-2">
                <Skeleton className="h-4 w-full" />
                <Skeleton className="h-4 w-full" />
                <Skeleton className="h-4 w-3/4" />
            </div>

            {/* User Info */}
            <div className="flex items-center gap-4 border-t border-border pt-4">
                <Skeleton className="h-12 w-12 shrink-0 rounded-full" />
                <div className="flex-1 space-y-2">
                    <div className="flex items-center gap-2">
                        <Skeleton className="h-4 w-24" />
                        <Skeleton className="h-4 w-12 rounded-full" />
                    </div>
                    <Skeleton className="h-3 w-16" />
                </div>
            </div>
        </div>
    );
};
