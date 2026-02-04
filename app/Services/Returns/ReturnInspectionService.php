<?php

namespace App\Services\Returns;

use App\Data\Returns\InspectionResultData;
use App\Enums\ReturnStatus;
use App\Events\Returns\ReturnInspected;
use App\Models\ReturnItemInspection;
use App\Models\ReturnOrder;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReturnInspectionService
{
    /**
     * تنفيذ الفحص (يستقبل مصفوفة نتائج لكل القطع)
     *
     * @param  Collection<int, InspectionResultData>  $results
     */
    public function inspect(ReturnOrder $returnOrder, Collection $results, $inspectorUser)
    {
        if ($returnOrder->status !== ReturnStatus::RECEIVED) {
            throw new Exception('يجب استلام الشحنة أولاً قبل الفحص.');
        }

        DB::transaction(function () use ($returnOrder, $results, $inspectorUser) {
            foreach ($results as $result) {
                $returnItem = $returnOrder->items()->findOrFail($result->return_item_id);

                ReturnItemInspection::create([
                    'return_item_id' => $returnItem->id,
                    'condition' => $result->condition,
                    'quantity' => $result->quantity,
                    'resolution' => $result->resolution,
                    'note' => $result->note,
                    'refund_amount' => $result->refund_amount,
                ]);
            }

            $returnOrder->update([
                'status' => ReturnStatus::INSPECTED,
                'inspected_at' => now(),
                'inspected_by' => $inspectorUser->id,
            ]);

            $returnOrder->history()->create([
                'status' => ReturnStatus::INSPECTED,
                'comment' => 'تم الفحص وتحديد القرار - بانتظار التنفيذ المالي',
                'actor_type' => get_class($inspectorUser),
                'actor_id' => $inspectorUser->id,
            ]);

            event(new ReturnInspected($returnOrder));
        });
    }
}
