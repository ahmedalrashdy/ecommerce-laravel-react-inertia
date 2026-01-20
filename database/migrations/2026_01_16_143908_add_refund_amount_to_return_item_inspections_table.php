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
        Schema::table('return_item_inspections', function (Blueprint $table) {
            $table->decimal('refund_amount', 10, 2)->nullable()->after('resolution');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('return_item_inspections', function (Blueprint $table) {
            $table->dropColumn('refund_amount');
        });
    }
};
