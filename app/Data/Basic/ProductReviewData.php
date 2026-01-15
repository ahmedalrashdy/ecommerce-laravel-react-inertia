<?php

namespace App\Data\Basic;

use App\Models\Review;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ProductReviewData extends Data
{
    public function __construct(
        public int $id,
        public string $userName,
        public ?string $userAvatar,
        public int $rating,
        public string $comment,
        public string $date,
        public $created_at,
        public bool $verified,
    ) {}

    public static function fromModel(Review $review): self
    {
        return new self(
            id: $review->id,
            userName: $review->user?->name ?? 'مستخدم مجهول',
            userAvatar: $review->user?->avatar,
            rating: $review->rating,
            comment: $review->comment,
            date: $review->created_at->translatedFormat('d M Y'),
            created_at: $review->created_at,
            verified: true,
        );
    }
}
