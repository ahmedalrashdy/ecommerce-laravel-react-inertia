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

        Schema::create('returns', function (Blueprint $table) {
            $table->id();

            // الربط
            $table->foreignId('order_id')->constrained()->cascadeOnDelete(); // الطلب الأصلي
            $table->foreignId('user_id')->constrained(); // العميل صاحب الطلب

            // المعرفات
            $table->string('return_number')->unique(); // مثال: RMA-2023-1001

            // الحالة العامة للمرتجع (تعتمد على Enum: ReturnStatus)
            // REQUESTED, APPROVED, SHIPPED_BACK, RECEIVED, INSPECTED, COMPLETED, REJECTED
            $table->unsignedTinyInteger('status')->default(1);

            // السبب العام (اختياري، لأن التفاصيل في العناصر)
            $table->text('reason')->nullable();

            // اللوجستيات (الشحن العكسي)
            $table->string('tracking_number')->nullable(); // بوليصة الإرجاع
            $table->string('shipping_label_url')->nullable(); // رابط البوليصة

            // القرار المالي والإداري
            $table->unsignedTinyInteger('refund_method')->nullable(); // Wallet, Bank, Original

            // التواريخ والتدقيق
            $table->text('admin_notes')->nullable(); // ملاحظات داخلية
            $table->timestamp('inspected_at')->nullable();
            $table->foreignId('inspected_by')->nullable()->constrained('users'); // الموظف الفاحص

            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index('return_number');
        });

        // 2. جدول عناصر المرتجع (التفاصيل والفحص)
        Schema::create('return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_id')->constrained('returns')->cascadeOnDelete();
            $table->foreignId('order_item_id')->constrained(); // لربطه بالسعر والمنتج الأصلي

            $table->unsignedInteger('quantity'); // الكمية المرجعة
            $table->string('reason'); // سبب العميل لهذا المنتج تحديداً (Wrong Size, Damaged)
            $table->timestamps();
        });
        Schema::create('return_item_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_item_id')->constrained()->cascadeOnDelete();

            // حالة القطع
            // حالة المنتج الفيزيائية (Enum: ItemCondition)
            // SEALED (جديد), OPEN_BOX (مفتوح), DAMAGED (تالف), WRONG_ITEM (خطأ)
            $table->unsignedTinyInteger('condition');
            $table->unsignedInteger('quantity');

            // القرار لهذه المجموعة
            // (Enum: ReturnResolution)
            // REFUND (إرجاع مال), REPLACEMENT (استبدال), REJECT (رفض وإعادة للعميل)
            $table->unsignedTinyInteger('resolution');
            // ملاحظة الفحص (مثلاً: "الشاشة مكسورة سوء استخدام")
            $table->string('note')->nullable();

            $table->timestamps();

            $table->unique(['return_item_id', 'condition', 'resolution'], 'unique_condition_resolution');
        });

        // 3. سجل تتبع المرتجع (الشفافية والنزاعات)
        Schema::create('return_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_id')->constrained('returns')->cascadeOnDelete();

            // الحالة التي انتقل إليها
            $table->unsignedTinyInteger('status');

            // تعليق (يظهر للعميل أو داخلي)
            $table->string('comment')->nullable();

            // التحكم بالظهور
            $table->boolean('is_visible_to_user')->default(true);

            // الفاعل (العميل، الموظف، النظام)
            $table->nullableMorphs('actor');

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_histories');
        Schema::dropIfExists('return_item_inspections');
        Schema::dropIfExists('return_items');
        Schema::dropIfExists('returns');
    }
};
