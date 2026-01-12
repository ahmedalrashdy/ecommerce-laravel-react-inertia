<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\Review;

class ReviewObserver
{
    /**
     * Handle the Review "created" event.
     */
    public function created(Review $review): void
    {
        if ($review->is_approved) {
            $this->updateProductReviewStats($review->product_id);
        }
    }

    /**
     * Handle the Review "updated" event.
     */
    public function updated(Review $review): void
    {
        $wasApproved = $review->getOriginal('is_approved');
        $isApproved = $review->is_approved;
        $ratingChanged = $review->wasChanged('rating');
        $productIdChanged = $review->wasChanged('product_id');

        // If product_id changed, update both old and new products
        if ($productIdChanged) {
            $oldProductId = $review->getOriginal('product_id');
            if ($oldProductId) {
                $this->updateProductReviewStats($oldProductId);
            }
            $this->updateProductReviewStats($review->product_id);
        } elseif ($wasApproved !== $isApproved || ($ratingChanged && $isApproved)) {
            // If approval status changed or rating changed
            $this->updateProductReviewStats($review->product_id);
        }
    }

    /**
     * Handle the Review "deleted" event.
     */
    public function deleted(Review $review): void
    {
        if ($review->is_approved) {
            $this->updateProductReviewStats($review->product_id);
        }
    }

    /**
     * Handle the Review "restored" event.
     */
    public function restored(Review $review): void
    {
        if ($review->is_approved) {
            $this->updateProductReviewStats($review->product_id);
        }
    }

    /**
     * Handle the Review "force deleted" event.
     */
    public function forceDeleted(Review $review): void
    {
        if ($review->is_approved) {
            $this->updateProductReviewStats($review->product_id);
        }
    }

    /**
     * Update product review statistics (count and average rating).
     */
    protected function updateProductReviewStats(int $productId): void
    {
        $product = Product::find($productId);

        if (! $product) {
            return;
        }

        // Calculate statistics for approved reviews only
        $stats = Review::where('product_id', $productId)
            ->where('is_approved', true)
            ->selectRaw('COUNT(*) as reviews_count, AVG(rating) as rating_avg')
            ->first();

        $product->update([
            'reviews_count' => $stats->reviews_count ?? 0,
            'rating_avg' => round($stats->rating_avg ?? 0, 2),
        ]);
    }
}
