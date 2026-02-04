<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * ENUM MAPPINGS (suggested)
     *
     * promotions.type:
     *   1 = automatic
     *   2 = coupon_required
     *
     * promotions.scope:
     *   1 = order
     *   2 = item
     *   3 = shipping
     *
     * promotions.action_type (bxgy_amount_off is intentionally NOT included):
     *   1  = percent_off
     *   2  = fixed_amount_off
     *   3  = free_shipping
     *   4  = bxgy_percent_off
     *   5  = bxgy_free
     *   6  = bundle_fixed_price
     *   7  = bundle_percent_off
     *   8  = tiered_qty_percent
     *   9  = tiered_qty_amount
     *   10 = tiered_spend_percent
     *   11 = tiered_spend_amount
     *   12 = free_gift (optional if you want it later)
     *
     * promotion_target_sets.role:
     *   1 = eligible          (items eligible for discount)
     *   2 = buy               (bxgy buy set)
     *   3 = get               (bxgy get set)
     *   4 = bundle_required   (bundle required components)
     *   5 = bundle_optional   (optional components / choose-from)
     *
     * target_type (used in promotion_target_set_items + promotion_exclusions):
     *   1 = product
     *   2 = category
     *   3 = brand
     *   4 = variant
     *   (extend if you have more normalized entities)
     */
    public function up(): void
    {
        // 1) Promotions (rule + action)
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();

            // Basics
            $table->string('name');
            $table->string('public_name')->nullable();
            $table->text('description')->nullable();

            // Activation & window
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable()->index();
            $table->timestamp('ends_at')->nullable()->index();

            // Type / Action / Scope
            $table->unsignedTinyInteger('type');        // automatic / coupon_required
            $table->unsignedTinyInteger('action_type'); // percent / fixed / shipping / bxgy / bundle / tiered ...
            $table->unsignedTinyInteger('scope')->default(1); // order/item/shipping

            // Priority & stacking
            $table->unsignedSmallInteger('priority')->default(100)->index();
            $table->boolean('is_stackable')->default(false);
            $table->boolean('stop_further')->default(false);

            /**
             * "value" semantics depend on action_type:
             * - percent_off: percent (0..100]
             * - fixed_amount_off: amount (>0)
             * - free_shipping: should be 0
             * - bxgy_percent_off: percent for GET items (0..100], bxgy_free => 100
             * - bundle_fixed_price: bundle price (>0)
             * - bundle_percent_off: percent (0..100]
             * - tiered_*: should be 0 (tiers live in config)
             */
            $table->decimal('value', 10, 2)->default(0);
            $table->decimal('max_discount_amount', 10, 2)->nullable();

            // Eligibility constraints
            $table->decimal('min_order_amount', 10, 2)->nullable();
            $table->unsignedInteger('min_quantity')->nullable();

            // Usage limits (denormalized counters)
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('usage_limit_per_user')->nullable();
            $table->unsignedInteger('used_count')->default(0);

            /**
             * conditions (JSONB): strict allowed keys only (validated in app layer)
             */
            $table->jsonb('conditions')->nullable();

            /**
             * config (JSONB): strict per action_type (validated in app layer)
             * - bxgy: repeatable/max_applications_per_order (optional)
             * - tiered: tiers array (required)
             * - others: must be null
             */
            $table->jsonb('config')->nullable();

            $table->timestamps();

            $table->index(['is_active', 'starts_at', 'ends_at'], 'promotions_active_window_idx');
            $table->index(['type', 'is_active'], 'promotions_type_active_idx');
            $table->index(['action_type', 'scope'], 'promotions_action_scope_idx');
        });

        // 2) Coupon Codes (only for coupon_required)
        Schema::create('coupon_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_id')->constrained('promotions')->cascadeOnDelete();

            $table->string('code'); // for admin display only
            $table->string('code_hash', 64)->unique(); // lookup key (sha256(normalized_code . app_key))

            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable()->index();
            $table->timestamp('ends_at')->nullable()->index();

            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('usage_limit_per_user')->nullable();
            $table->unsignedInteger('used_count')->default(0);

            $table->timestamps();

            $table->index(['promotion_id', 'is_active'], 'coupon_promo_active_idx');
        });

        /**
         * 3) Target Sets (replaces promotion_targets)
         * Each promotion can have 0..N sets with specific roles (eligible/buy/get/bundle_required/bundle_optional)
         */
        Schema::create('promotion_target_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_id')->constrained('promotions')->cascadeOnDelete();

            $table->unsignedTinyInteger('role'); // eligible/buy/get/bundle_required/bundle_optional

            // Group-level quantity rules (useful for bxgy/bundle mix & match)
            $table->unsignedInteger('min_qty')->nullable();
            $table->unsignedInteger('max_qty')->nullable();
            $table->unsignedInteger('min_distinct')->nullable();

            // How to pick items when more than required exist (esp. GET set)
            $table->string('selection_strategy', 32)->nullable(); // cheapest/most_expensive/customer_choice


            $table->timestamps();

            $table->index(['promotion_id', 'role'], 'promo_sets_role_idx');
        });

        // 4) Target Set Items
        Schema::create('promotion_target_set_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('target_set_id')->constrained('promotion_target_sets')->cascadeOnDelete();

            $table->unsignedTinyInteger('target_type'); // product/category/brand/variant
            $table->unsignedBigInteger('target_id');

            // Mainly for bundles: required quantity of this item within the set
            $table->unsignedInteger('required_qty')->nullable();
            $table->boolean('is_required')->default(true);

            $table->timestamps();

            $table->unique(['target_set_id', 'target_type', 'target_id'], 'promo_set_item_unique');
            $table->index(['target_type', 'target_id'], 'promo_set_item_lookup_idx');
            $table->index(['target_set_id'], 'promo_set_item_set_idx');
        });

        // 5) Exclusions (kept as requested)
        Schema::create('promotion_exclusions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_id')->constrained('promotions')->cascadeOnDelete();

            $table->unsignedTinyInteger('target_type'); // product/category/brand/variant
            $table->unsignedBigInteger('target_id');

            $table->timestamps();

            $table->unique(['promotion_id', 'target_type', 'target_id'], 'promo_exclusion_unique');
            $table->index(['target_type', 'target_id'], 'promo_exclusion_lookup_idx');
        });

        // 6) Promotion usages (audit + anti-fraud)
        Schema::create('promotion_usages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('promotion_id')->constrained('promotions')->cascadeOnDelete();
            $table->foreignId('coupon_code_id')->nullable()->constrained('coupon_codes')->nullOnDelete();

            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->unsignedTinyInteger('scope'); // order/item/shipping
            $table->decimal('discount_amount', 10, 2);

            $table->jsonb('meta')->nullable();

            $table->timestamp('used_at')->useCurrent();
            $table->timestamps();

            $table->unique(['order_id', 'promotion_id', 'scope'], 'order_promo_scope_unique');
            $table->unique(['order_id', 'coupon_code_id'], 'order_coupon_unique');

            $table->index(['user_id', 'promotion_id'], 'user_promo_idx');
            $table->index(['promotion_id', 'used_at'], 'promo_used_at_idx');
        });

        // 7) Order promotions (invoice snapshot)
        Schema::create('order_promotions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('promotion_id')->nullable()->constrained('promotions')->nullOnDelete();
            $table->foreignId('coupon_code_id')->nullable()->constrained('coupon_codes')->nullOnDelete();

            $table->unsignedTinyInteger('scope'); // order/item/shipping
            $table->decimal('discount_amount', 10, 2);

            $table->jsonb('snapshot'); // frozen invoice rule details

            $table->timestamps();

            $table->unique(['order_id', 'promotion_id', 'coupon_code_id', 'scope'], 'order_promo_unique');
            $table->index(['order_id', 'scope'], 'order_promo_scope_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_promotions');
        Schema::dropIfExists('promotion_usages');
        Schema::dropIfExists('promotion_exclusions');
        Schema::dropIfExists('promotion_target_set_items');
        Schema::dropIfExists('promotion_target_sets');
        Schema::dropIfExists('coupon_codes');
        Schema::dropIfExists('promotions');
    }
};
