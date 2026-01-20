<?php

namespace App\Data\Basic;

use App\Models\ReturnHistory;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ReturnTimelineData extends Data
{
    public function __construct(
        public int $status,
        public string $statusLabel,
        public ?string $comment,
        public string $createdAt,
    ) {}

    public static function fromModel(ReturnHistory $history): self
    {
        return self::from([
            'status' => $history->status->value,
            'statusLabel' => $history->status->getLabel(),
            'comment' => $history->comment,
            'createdAt' => $history->created_at->translatedFormat('d F Y - H:i'),
        ]);
    }
}
