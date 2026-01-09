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
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // بيانات التواصل (يدوي)
            $table->string('contact_person');
            $table->string('contact_phone');
            $table->string('country')->nullable();      // الدولة
            $table->string('state')->nullable();        // المنطقة/المحافظة (Admin Level 1)
            $table->string('city')->nullable();         // المدينة
            $table->string('postal_code')->nullable();
            // السماح للمستخدم بالتعديل والإضافه
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();

            $table->boolean('is_default_shipping')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};
