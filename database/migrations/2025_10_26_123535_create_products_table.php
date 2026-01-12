<?php

use App\Enums\AttributeType;
use App\Enums\ProductStatus;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->longText('description')->nullable();
            $table->jsonb('specifications')->nullable();
            $table->unsignedTinyInteger('status')->default(ProductStatus::Draft);
            $table->boolean('featured')->default(false);
            // normlization
            $table->unsignedBigInteger('sales_count')->default(0);
            $table->unsignedInteger('favorites_count')->default(0);
            $table->decimal('rating_avg', 3, 2)->default(value: 0);
            $table->unsignedInteger('reviews_count')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedTinyInteger('type')->default(AttributeType::Text);
        });

        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_id')->constrained()->cascadeOnDelete();
            $table->string('value');
            $table->string('color_code')->nullable();
            $table->unique(['attribute_id', 'value']);
            // Composite Foreign Key  لمنع المستخدم من إدخال قيمه سمه لا تنتمي الى السمة الصحيحه في جدول قيم سمات المتغير
            $table->unique(['id', 'attribute_id']);
        });
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('sku')->unique();
            $table->decimal('price', 10, 2);
            $table->decimal('compare_at_price', 10, 2)->nullable();
            $table->unsignedInteger('quantity')->default(0);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
        Schema::create('attribute_value_product_variant', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attribute_value_id')->constrained()->cascadeOnDelete();

            $table->foreignId('attribute_id')->constrained()->cascadeOnDelete();

            $table->unique(['product_variant_id', 'attribute_id'], 'one_value_per_attribute_rule');

            $table->foreign(['attribute_value_id', 'attribute_id'], 'value_belong_to_correct_attribute_rule')
                ->references(['id', 'attribute_id'])
                ->on('attribute_values')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_value_product_variant');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('attribute_values');
        Schema::dropIfExists('attributes');
        Schema::dropIfExists('products');
    }
};
