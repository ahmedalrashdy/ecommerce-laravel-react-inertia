<?php

use App\Enums\OrderType;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('order_number')->unique();

            // حقول اساسيه لتعامل مع  طلبات الإستبدال و إعادة الشحن
            $table->foreignId('parent_order_id')->nullable()->constrained('orders')->cascadeOnDelete();
            $table->unsignedTinyInteger('type')->default(OrderType::NORMAL);

            // لا نضع حاله افتراضية لزيادة الأمان
            $table->unsignedTinyInteger('status');
            $table->unsignedTinyInteger('payment_method');
            $table->unsignedTinyInteger('payment_status');

            $table->jsonb('shipping_address_snapshot');

            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2);

            $table->timestamp('paid_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->string('tracking_number')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('status');
            $table->index('payment_status');
            $table->index('created_at');
            $table->index(['status', 'payment_status']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();

            $table->jsonb('product_variant_snapshot');

            $table->decimal('price', 10, 2);
            $table->unsignedInteger('quantity');
            $table->decimal('discount_amount', 10, 2)->default(0);

            $table->timestamps();

            // Indexes
            $table->index('product_id');
            $table->index('product_variant_id');
            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
