import { Skeleton } from '@/components/ui/skeleton';
import * as React from 'react';

export const ReviewsSummarySkeleton: React.FC = () => {
    return (
        <div className="animate-pulse space-y-6">
            {/* Summary Header */}
            <div className="flex flex-col gap-6 rounded-xl border bg-card p-6 md:flex-row md:items-center md:gap-10">
                {/* Overall Rating */}
                <div className="flex flex-col items-center gap-2">
                    <Skeleton className="h-16 w-20" />
                    <div className="flex gap-0.5">
                        {Array.from({ length: 5 }).map((_, i) => (
                            <Skeleton
                                key={i}
                                className="h-5 w-5"
                            />
                        ))}
                    </div>
                    <Skeleton className="h-4 w-24" />
                </div>

                {/* Rating Distribution */}
                <div className="flex-1 space-y-2">
                    {[5, 4, 3, 2, 1].map((star) => (
                        <div
                            key={star}
                            className="flex items-center gap-3"
                        >
                            <Skeleton className="h-4 w-8" />
                            <Skeleton className="h-2 flex-1 rounded-full" />
                            <Skeleton className="h-4 w-8" />
                        </div>
                    ))}
                </div>
            </div>

            {/* Reviews List */}
            <div className="space-y-4">
                {Array.from({ length: 3 }).map((_, index) => (
                    <ReviewCardSkeleton key={index} />
                ))}
            </div>
        </div>
    );
};

const ReviewCardSkeleton: React.FC = () => {
    return (
        <div className="rounded-xl border bg-card p-4 sm:p-6">
            <div className="flex items-start gap-3 sm:gap-4">
                <Skeleton className="h-10 w-10 rounded-full sm:h-12 sm:w-12" />
                <div className="min-w-0 flex-1 space-y-2">
                    <div className="flex flex-wrap items-center gap-2">
                        <Skeleton className="h-4 w-24" />
                        <Skeleton className="h-5 w-16 rounded-full" />
                        <Skeleton className="mr-auto h-3 w-20" />
                    </div>
                    <div className="flex gap-0.5">
                        {Array.from({ length: 5 }).map((_, i) => (
                            <Skeleton
                                key={i}
                                className="h-3.5 w-3.5"
                            />
                        ))}
                    </div>
                </div>
            </div>

            <div className="mt-4 space-y-2">
                <Skeleton className="h-4 w-full" />
                <Skeleton className="h-4 w-full" />
                <Skeleton className="h-4 w-3/4" />
            </div>

            <div className="mt-4 flex items-center gap-4 border-t pt-4">
                <Skeleton className="h-4 w-32" />
                <div className="mr-auto flex items-center gap-2">
                    <Skeleton className="h-8 w-16 rounded-full" />
                    <Skeleton className="h-8 w-16 rounded-full" />
                </div>
            </div>
        </div>
    );
};
