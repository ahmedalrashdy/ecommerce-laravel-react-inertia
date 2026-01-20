<?php

use App\Models\Order;
use App\Models\OrderHistory;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Order::query()
            ->where('status', 1)
            ->update(['status' => 2]);

        OrderHistory::query()
            ->where('status', 1)
            ->update(['status' => 2]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
