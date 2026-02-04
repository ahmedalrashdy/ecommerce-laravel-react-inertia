<?php

namespace App\Data\Basic;

use App\Models\ReturnOrder;
use Brick\Money\Money;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\Hidden;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ReturnDetailsData extends Data
{
    public function __construct(
        #[Hidden]
        public int $returnId,
        public string $returnNumber,
        public int $status,
        public string $statusLabel,
        public string $createdAt,
        public ?string $orderNumber,
        public int $itemsCount,
        public string $refundAmount,
        public string $formattedRefundAmount,
        public ?string $refundMethodLabel,
        public ?string $trackingNumber,
        public ?string $shippingLabelUrl,
        #[LiteralTypeScriptType('ReturnItemDetailsData[]')]
        #[DataCollectionOf(ReturnItemDetailsData::class)]
        public Collection $items,
        #[LiteralTypeScriptType('ReturnTimelineData[]')]
        #[DataCollectionOf(ReturnTimelineData::class)]
        public Collection $timeline,
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
            'status' => $returnOrder->status->value,
            'statusLabel' => $returnOrder->status->getLabel(),
            'createdAt' => $returnOrder->created_at->translatedFormat('d F Y'),
            'orderNumber' => $returnOrder->order?->order_number,
            'itemsCount' => $returnOrder->items->count(),
            'refundAmount' => $returnOrder->refund_amount ?? '0',
            'formattedRefundAmount' => \App\Data\Casts\MoneyCast::formatMoney($refundAmount),
            'refundMethodLabel' => $returnOrder->refund_method?->getLabel(),
            'trackingNumber' => $returnOrder->tracking_number,
            'shippingLabelUrl' => $returnOrder->shipping_label_url,
            'items' => ReturnItemDetailsData::collect($returnOrder->items),
            'timeline' => ReturnTimelineData::collect($returnOrder->history),
        ]);
    }
}
