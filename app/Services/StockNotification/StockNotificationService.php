<?php

namespace App\Services\StockNotification;

use App\Models\ProductVariant;
use App\Models\StockNotification;
use App\Models\User;

class StockNotificationService
{
    /**
     * Subscribe user to stock notification for a product variant
     *
     * @throws \Illuminate\Database\QueryException if subscription already exists
     */
    public function subscribe(User $user, ProductVariant $variant): StockNotification
    {
        // Check if subscription already exists
        $existing = StockNotification::where('user_id', $user->id)
            ->where('product_variant_id', $variant->id)
            ->first();
        if ($existing) {
            // If already notified, reset it
            if ($existing->notified) {
                $existing->update([
                    'notified' => false,
                    'notified_at' => null,
                ]);
            }

            return $existing;
        }

        // Create new subscription
        return StockNotification::create([
            'user_id' => $user->id,
            'product_variant_id' => $variant->id,
            'notified' => false,
        ]);
    }
}
