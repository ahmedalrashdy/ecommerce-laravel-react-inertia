<?php

namespace App\Data\Basic;

use App\Models\ReturnItem;
use Brick\Money\Money;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ReturnItemDetailsData extends Data
{
    /** @param  array<int, array<string, string>>  $attributes */
    public function __construct(
        public int $id,
        public string $productName,
        public ?string $image,
        public array $attributes,
        public string $reason,
        public int $quantity,
        public string $unitPrice,
        public string $formattedUnitPrice,
        public string $total,
        public string $formattedTotal,
        public string $inspectionStatus,
        #[LiteralTypeScriptType('ReturnInspectionData[]')]
        #[DataCollectionOf(ReturnInspectionData::class)]
        public Collection $inspections,
    ) {}

    public static function fromModel(ReturnItem $returnItem): self
    {
        $orderItem = $returnItem->orderItem;
        $unitPrice = Money::of($orderItem->price, 'USD');
        $total = $unitPrice->multipliedBy($returnItem->quantity);
        $inspections = ReturnInspectionData::collect($returnItem->inspections);

        return self::from([
            'id' => $returnItem->id,
            'productName' => $orderItem->product_name,
            'image' => $orderItem->product_variant_snapshot['variant']['default_image'] ?? null,
            'attributes' => $orderItem->attributes_list,
            'reason' => $returnItem->reason,
            'quantity' => $returnItem->quantity,
            'unitPrice' => $orderItem->price,
            'formattedUnitPrice' => \App\Data\Casts\MoneyCast::formatMoney($unitPrice),
            'total' => $total->getAmount()->toScale(2)->__toString(),
            'formattedTotal' => \App\Data\Casts\MoneyCast::formatMoney($total),
            'inspectionStatus' => $inspections->isNotEmpty()
                ? 'تم الفحص'
                : 'بانتظار الفحص',
            'inspections' => $inspections,
        ]);
    }
}
