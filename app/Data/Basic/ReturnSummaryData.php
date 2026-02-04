<?php

namespace App\Data\Basic;

use App\Models\ReturnOrder;
use Brick\Money\Money;
use Spatie\LaravelData\Attributes\Hidden;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ReturnSummaryData extends Data
{
    public function __construct(
        #[Hidden]
        public int $returnId,
        public string $returnNumber,
        public ?string $orderNumber,
        public int $status,
        public string $statusLabel,
        public int $itemsCount,
        public string $refundAmount,
        public string $formattedRefundAmount,
        public string $createdAt,
    ) {}

    public function getReturnId(): int
    {
        return $this->returnId;
    }

    public static function fromModel(ReturnOrder $returnOrder): self
    {
        $refundAmount = Money::of($returnOrder->refund_amount ?? '0', 'USD');

        return self::from([
            'returnId' => $returnOrder->id,
            'returnNumber' => $returnOrder->return_number,
            'orderNumber' => $returnOrder->order?->order_number,
            'status' => $returnOrder->status->value,
            'statusLabel' => $returnOrder->status->getLabel(),
            'itemsCount' => $returnOrder->items_count,
            'refundAmount' => $returnOrder->refund_amount,
            'formattedRefundAmount' => \App\Data\Casts\MoneyCast::formatMoney($refundAmount),
            'createdAt' => $returnOrder->created_at->toDateString(),
        ]);
    }
}
