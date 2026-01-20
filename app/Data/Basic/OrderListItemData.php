<?php

namespace App\Data\Basic;

use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\OrderItem;
use Brick\Money\Money;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class OrderListItemData extends Data
{
    public function __construct(
        public int $id,
        public string $orderNumber,
        public int $status,
        public string $statusLabel,
        public int $paymentStatus,
        public string $paymentStatusLabel,
        public int $itemsCount,
        public string $grandTotal,
        public string $formattedGrandTotal,
        public string $createdAt,
        public string $createdAtIso,
        public string $expectedDelivery,
        public string $shippingName,
        public ?string $trackingNumber,
        #[LiteralTypeScriptType('OrderItemPreviewData[]')]
        #[DataCollectionOf(OrderItemPreviewData::class)]
        public Collection $itemsPreview,
        public int $moreItemsCount,
        public bool $canPay,
        public string $searchText,
    ) {}

    public static function fromModel(Order $order): self
    {
        $total = Money::of($order->grand_total, 'USD');
        $itemsPreview = $order->items
            ->take(3)
            ->map(function (OrderItem $item) {
                return [
                    'id' => $item->id,
                    'name' => $item->product_name,
                    'image' => $item->product_variant_snapshot['variant']['default_image'] ?? null,
                ];
            })
            ->values();
        $searchTokens = $order->items
            ->flatMap(function (OrderItem $item) {
                return [
                    $item->product_name,
                    $item->product?->category?->name,
                    $item->product?->brand?->name,
                ];
            })
            ->filter()
            ->unique()
            ->values();

        return self::from([
            'id' => $order->id,
            'orderNumber' => $order->order_number,
            'status' => $order->status->value,
            'statusLabel' => $order->status->getLabel(),
            'paymentStatus' => $order->payment_status->value,
            'paymentStatusLabel' => $order->payment_status->getLabel(),
            'itemsCount' => $order->items_count,
            'grandTotal' => $order->grand_total,
            'formattedGrandTotal' => \App\Data\Casts\MoneyCast::formatMoney($total),
            'createdAt' => $order->created_at->translatedFormat('d F Y'),
            'createdAtIso' => $order->created_at->toDateString(),
            'expectedDelivery' => $order->created_at->copy()->addDays(3)->translatedFormat('d F Y'),
            'shippingName' => $order->shipping_address_snapshot['contact_person'] ?? '—',
            'trackingNumber' => $order->tracking_number,
            'itemsPreview' => $itemsPreview,
            'moreItemsCount' => max($order->items_count - $itemsPreview->count(), 0),
            'canPay' => in_array($order->payment_status, [PaymentStatus::PENDING, PaymentStatus::FAILED], true),
            'searchText' => Str::lower($searchTokens->implode(' ')),
        ]);
    }
}
