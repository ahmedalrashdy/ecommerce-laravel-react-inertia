<?php

namespace App\Data\Basic;

use App\Models\ReturnItemInspection;
use Brick\Money\Money;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ReturnInspectionData extends Data
{
    public function __construct(
        public string $conditionLabel,
        public string $resolutionLabel,
        public int $quantity,
        public ?string $refundAmount,
        public ?string $formattedRefundAmount,
        public ?string $note,
    ) {}

    public static function fromModel(ReturnItemInspection $inspection): self
    {
        $formattedAmount = null;

        if ($inspection->refund_amount !== null) {
            $formattedAmount = \App\Data\Casts\MoneyCast::formatMoney(
                Money::of($inspection->refund_amount, 'USD')
            );
        }

        return self::from([
            'conditionLabel' => $inspection->condition->getLabel(),
            'resolutionLabel' => $inspection->resolution->getLabel(),
            'quantity' => $inspection->quantity,
            'refundAmount' => $inspection->refund_amount,
            'formattedRefundAmount' => $formattedAmount,
            'note' => $inspection->note,
        ]);
    }
}
