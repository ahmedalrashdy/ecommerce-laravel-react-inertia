// import { ReviewsSummarySkeleton } from '@/components/feedback/skeletons/ReviewsSummarySkeleton';
import { RatingStars } from '@/components/common/review-card';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Progress } from '@/components/ui/progress';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { storageUrl } from '@/lib/utils';
import { InfiniteScroll, usePage } from '@inertiajs/react';
import { Check, Loader2, Star } from 'lucide-react';
import * as React from 'react';

interface PaginatedReviews {
    data: App.Data.Basic.ProductReviewData[];
    meta?: {
        current_page: number;
        next_page: number | null;
        prev_page: number | null;
    };
}

interface PageProps {
    reviewsSummary?: App.Data.Basic.ProductReviewsSummaryData;
    reviews?: PaginatedReviews;
    [key: string]: unknown;
}

type SpecificationItem = { key: string; value: string };
type SpecificationsInput =
    | SpecificationItem[]
    | Record<string, string>
    | null
    | undefined;

/**
 * Normalizes specifications data to a consistent array format
 * Supports both array format [{key: string, value: string}] and object format {key: value}
 */
const normalizeSpecifications = (
    specs: SpecificationsInput,
): SpecificationItem[] => {
    if (!specs) {
        return [];
    }

    // If it's already an array
    if (Array.isArray(specs)) {
        return specs;
    }

    // If it's an object, convert it to array format
    if (typeof specs === 'object') {
        return Object.entries(specs).map(([key, value]) => ({
            key,
            value: String(value),
        }));
    }

    return [];
};

export const DetailsTabsSection = ({
    productDetails,
}: {
    productDetails: App.Data.Basic.ProductDetailsData;
}) => {
    const specifications = normalizeSpecifications(
        productDetails.specifications as SpecificationsInput,
    );

    return (
        <div className="mt-12">
            <Tabs
                defaultValue="specs"
                className="w-full"
            >
                <TabsList className="h-auto w-full justify-start gap-6 rounded-none border-b bg-transparent p-0">
                    {specifications.length > 0 && (
                        <TabsTrigger
                            value="specs"
                            className="rounded-none border-b-2 border-transparent px-2 py-3 text-sm font-semibold data-[state=active]:border-primary data-[state=active]:bg-transparent"
                        >
                            المواصفات
                        </TabsTrigger>
                    )}
                    {productDetails.reviews > 0 && (
                        <TabsTrigger
                            value="reviews"
                            className="rounded-none border-b-2 border-transparent px-2 py-3 text-sm font-semibold data-[state=active]:border-primary data-[state=active]:bg-transparent"
                        >
                            التقييمات ({productDetails.reviews})
                        </TabsTrigger>
                    )}
                </TabsList>

                <div className="py-6">
                    <TabsContent
                        value="specs"
                        className="animate-in duration-300 fade-in-50"
                    >
                        <div className="overflow-hidden rounded-lg border">
                            <table className="w-full text-sm">
                                <tbody className="divide-y">
                                    {specifications.map((spec, index) => (
                                        <tr
                                            key={`${spec.key}-${index}`}
                                            className="bg-background"
                                        >
                                            <td className="w-1/3 px-4 py-3 font-semibold text-foreground">
                                                {spec.key}
                                            </td>
                                            <td className="px-4 py-3 text-muted-foreground">
                                                {spec.value}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </TabsContent>
                    <TabsContent value="reviews">
                        <ReviewsContent />
                    </TabsContent>
                </div>
            </Tabs>
        </div>
    );
};

const ReviewsContent: React.FC = () => {
    const { reviewsSummary, reviews } = usePage<PageProps>().props;

    if (!reviewsSummary || reviewsSummary.totalReviews === 0) {
        return (
            <div className="flex flex-col items-center justify-center rounded-lg border border-dashed bg-muted/20 py-10">
                <Star className="mb-2 h-8 w-8 text-muted-foreground/30" />
                <p className="mb-4 text-sm text-muted-foreground">
                    لا توجد تقييمات لهذا المنتج بعد
                </p>
                <Button
                    variant="outline"
                    size="sm"
                >
                    أضف تقييمك
                </Button>
            </div>
        );
    }

    return (
        <div className="space-y-6">
            {/* Reviews Summary */}
            <ReviewsSummaryHeader summary={reviewsSummary} />
            <div className="custom-scrollbar h-[520px] overflow-y-auto">
                <InfiniteScroll
                    data="reviews"
                    manual={true}
                    loading={<ReviewsLoadingIndicator />}
                    next={({ loading, fetch, hasMore, manualMode }) =>
                        manualMode &&
                        hasMore && (
                            <div className="flex justify-center pt-4">
                                <Button
                                    variant="outline"
                                    onClick={fetch}
                                    disabled={loading}
                                    className="gap-2"
                                >
                                    {loading ? (
                                        <>
                                            <Loader2 className="h-4 w-4 animate-spin" />
                                            جاري التحميل...
                                        </>
                                    ) : (
                                        'تحميل المزيد من التقييمات'
                                    )}
                                </Button>
                            </div>
                        )
                    }
                >
                    <div className="space-y-4">
                        {reviews?.data.map((review) => (
                            <ReviewCard
                                key={review.id}
                                review={review}
                            />
                        ))}
                    </div>
                </InfiniteScroll>
            </div>
        </div>
    );
};

const ReviewsLoadingIndicator: React.FC = () => (
    <div className="flex items-center justify-center gap-2 px-4 py-1">
        <Loader2 className="h-6 w-6 animate-spin text-primary" />
        <span className="text-sm text-muted-foreground">
            جاري تحميل المزيد من التقييمات...
        </span>
    </div>
);

const ReviewsSummaryHeader: React.FC<{
    summary: App.Data.Basic.ProductReviewsSummaryData;
}> = ({ summary }) => {
    return (
        <div className="flex flex-col gap-6 rounded-xl border bg-card p-6 md:flex-row md:items-center md:gap-10">
            {/* Overall Rating */}
            <div className="flex flex-col items-center gap-2 text-center">
                <span className="text-5xl font-bold text-foreground">
                    {summary.averageRating.toFixed(1)}
                </span>
                <RatingStars
                    rating={summary.averageRating}
                    size="lg"
                />
                <span className="text-sm text-muted-foreground">
                    بناءً على {summary.totalReviews} تقييم
                </span>
            </div>

            {/* Rating Distribution */}
            <div className="flex-1 space-y-2">
                {[5, 4, 3, 2, 1].map((star) => {
                    const count = summary.distribution[star] ?? 0;
                    const percentage =
                        summary.totalReviews > 0
                            ? (count / summary.totalReviews) * 100
                            : 0;

                    return (
                        <div
                            key={star}
                            className="flex items-center gap-3"
                        >
                            <span className="w-8 text-sm font-medium text-muted-foreground">
                                {star} ★
                            </span>
                            <Progress
                                value={percentage}
                                className="h-2 flex-1"
                            />
                            <span className="w-8 text-sm text-muted-foreground">
                                {count}
                            </span>
                        </div>
                    );
                })}
            </div>
        </div>
    );
};

const ReviewCard: React.FC<{ review: App.Data.Basic.ProductReviewData }> = ({
    review,
}) => {
    return (
        <div className="group rounded-2xl border border-border/50 bg-linear-to-br from-card to-card/80 p-4 transition-all duration-300 hover:shadow-lg hover:shadow-primary/5 sm:p-6">
            <div className="flex items-start gap-3 sm:gap-4">
                <Avatar className="h-10 w-10 ring-2 ring-primary/10 ring-offset-2 ring-offset-background sm:h-12 sm:w-12">
                    <AvatarImage
                        src={
                            review.userAvatar
                                ? storageUrl(review.userAvatar)
                                : undefined
                        }
                    />
                    <AvatarFallback className="bg-linear-to-br from-primary/20 to-primary/10 font-bold text-primary">
                        {review.userName.charAt(0)}
                    </AvatarFallback>
                </Avatar>
                <div className="min-w-0 flex-1">
                    <div className="mb-1 flex flex-wrap items-center gap-2">
                        <span className="font-semibold text-foreground">
                            {review.userName}
                        </span>
                        {review.verified && (
                            <Badge className="h-5 gap-1 border-success/20 bg-success/10 text-[10px] text-success hover:bg-success/20">
                                <Check className="h-3 w-3" />
                                مشتري موثق
                            </Badge>
                        )}
                        <span className="mr-auto text-xs text-muted-foreground">
                            {review.date}
                        </span>
                    </div>
                    <RatingStars
                        rating={review.rating}
                        size="sm"
                    />
                </div>
            </div>

            <p className="mt-4 text-sm leading-relaxed text-foreground/90 sm:text-base">
                {review.comment}
            </p>
        </div>
    );
};
