<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('gateway', 32)->nullable()->after('user_id');
            $table->string('idempotency_key')->nullable()->unique()->after('gateway');
            $table->string('event_id')->nullable()->unique()->after('idempotency_key');
            $table->string('event_type')->nullable()->after('event_id');
            $table->string('checkout_session_id')->nullable()->index()->after('event_type');
            $table->string('payment_intent_id')->nullable()->index()->after('checkout_session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropUnique(['idempotency_key']);
            $table->dropUnique(['event_id']);
            $table->dropIndex(['checkout_session_id']);
            $table->dropIndex(['payment_intent_id']);
            $table->dropColumn([
                'gateway',
                'idempotency_key',
                'event_id',
                'event_type',
                'checkout_session_id',
                'payment_intent_id',
            ]);
        });
    }
};
