<?php

namespace App\Data\Basic;

use App\Models\Product;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ProductReviewsSummaryData extends Data
{
    /**
     * @param  array<int, int>  $distribution
     */
    public function __construct(
        public float $averageRating,
        public int $totalReviews,
        /** @var array<int, int> */
        public array $distribution,
    ) {}

    public static function fromProduct(Product $product): self
    {
        $stats = $product->reviews()
            ->where('is_approved', true)
            ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total')
            ->first();

        $distribution = $product->reviews()
            ->where('is_approved', true)
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        $formattedDistribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $formattedDistribution[$i] = $distribution[$i] ?? 0;
        }

        return new self(
            averageRating: (float) ($stats->avg_rating ?? 0),
            totalReviews: (int) ($stats->total ?? 0),
            distribution: $formattedDistribution,
        );
    }
}
