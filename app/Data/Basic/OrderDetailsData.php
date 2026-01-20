<?php

namespace App\Data\Basic;

use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class OrderDetailsData extends Data
{
    /** @param  array<string, string|null>  $shippingAddress */
    public function __construct(
        public int $id,
        public string $orderNumber,
        public int $status,
        public string $statusLabel,
        public int $paymentStatus,
        public string $paymentStatusLabel,
        public string $paymentMethodLabel,
        public ?string $trackingNumber,
        public string $createdAt,
        public ?string $expectedDelivery,
        public bool $canReturn,
        public bool $canCancel,
        public ?string $returnWindowEndsAt,
        public bool $canReview,
        public array $shippingAddress,
        public bool $canPay,
        #[LiteralTypeScriptType('OrderDetailsItemData[]')]
        #[DataCollectionOf(OrderDetailsItemData::class)]
        public Collection $items,
    ) {}

    public static function fromModel(Order $order, User $user): self
    {
        $order->loadMissing('items');

        $deliveredAt = $order->history()
            ->where('status', OrderStatus::DELIVERED)
            ->latest('created_at')
            ->first()
            ?->created_at;

        $returnWindowEndsAt = $deliveredAt
            ? $deliveredAt->copy()->addDays(14)
            : null;

        $canReturn = $order->status === OrderStatus::DELIVERED
            && $returnWindowEndsAt
            && now()->lessThanOrEqualTo($returnWindowEndsAt)
            && $order->type !== OrderType::RETURN_SHIPMENT;

        $canCancel = $order->payment_status === PaymentStatus::PAID
            && ! in_array($order->status, [
                OrderStatus::SHIPPED,
                OrderStatus::DELIVERED,
                OrderStatus::CANCELLED,
            ], true);

        $reviews = Review::query()
            ->where('user_id', $user->id)
            ->whereIn('product_id', $order->items->pluck('product_id'))
            ->get()
            ->keyBy('product_id');

        $items = $order->items->map(function ($item) use ($reviews) {
            return OrderDetailsItemData::fromModel($item, $reviews->get($item->product_id));
        });

        return self::from([
            'id' => $order->id,
            'orderNumber' => $order->order_number,
            'status' => $order->status->value,
            'statusLabel' => $order->status->getLabel(),
            'paymentStatus' => $order->payment_status->value,
            'paymentStatusLabel' => $order->payment_status->getLabel(),
            'paymentMethodLabel' => $order->payment_method->getLabel(),
            'trackingNumber' => $order->tracking_number,
            'createdAt' => $order->created_at->translatedFormat('d F Y'),
            'expectedDelivery' => $order->created_at->copy()->addDays(3)->translatedFormat('d F Y'),
            'canReturn' => $canReturn,
            'canCancel' => $canCancel,
            'returnWindowEndsAt' => $returnWindowEndsAt?->translatedFormat('d F Y'),
            'canReview' => $order->status === OrderStatus::DELIVERED,
            'shippingAddress' => $order->shipping_address_snapshot,
            'canPay' => in_array($order->payment_status, [PaymentStatus::PENDING, PaymentStatus::FAILED], true),
            'items' => $items,
        ]);
    }
}
