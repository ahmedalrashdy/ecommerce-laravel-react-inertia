<?php

namespace App\Services\Returns;

use App\Enums\ReturnStatus;
use App\Models\ReturnOrder;
use Illuminate\Support\Str;

class ReturnLogisticsService
{
    /**
     * الموافقة المبدئية وإصدار البوليصة
     */
    public function approve(ReturnOrder $returnOrder, $adminUser)
    {
        if ($returnOrder->status !== ReturnStatus::REQUESTED) {
            throw new \Exception("لا يمكن الموافقة على طلب ليس في حالة 'جديد'.");
        }

        $returnOrder->update([
            'status' => ReturnStatus::APPROVED,
            // محاكاة إصدار بوليصة شحن
            'tracking_number' => 'RET-TRK-'.Str::random(10),
            'shipping_label_url' => 'https://api.shipping.com/labels/dummy.pdf',
        ]);

        $this->logHistory($returnOrder, ReturnStatus::APPROVED, 'تمت الموافقة وإصدار البوليصة', $adminUser);
    }

    /**
     * تسجيل وصول الشحنة للمستودع
     */
    public function markAsReceived(ReturnOrder $returnOrder, $adminUser)
    {
        // يمكن الانتقال من APPROVED أو SHIPPED_BACK
        $returnOrder->update([
            'status' => ReturnStatus::RECEIVED,
        ]);

        $this->logHistory($returnOrder, ReturnStatus::RECEIVED, 'وصلت الشحنة للمستودع - بانتظار الفحص', $adminUser);
    }

    protected function logHistory($returnOrder, $status, $comment, $actor)
    {
        $returnOrder->history()->create([
            'status' => $status,
            'comment' => $comment,
            'actor_type' => get_class($actor),
            'actor_id' => $actor->id,
        ]);
    }
}
