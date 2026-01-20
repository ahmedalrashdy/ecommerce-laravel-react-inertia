<?php

use App\Enums\StockMovementType;
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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->unsignedTinyInteger('type')->index();

            $table->integer('quantity'); // قيمة موجبة للإضافة، وسالبة للخصم
            $table->integer('quantity_before'); // لغرض التتبع الدقيق
            $table->integer('quantity_after');

            $table->nullableMorphs('sourceable'); // could be Order, Return,PurchaseOrder(future), or User (Admin)

            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
