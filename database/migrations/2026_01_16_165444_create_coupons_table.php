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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();

            $table->string('type');
            $table->decimal('value', 10, 3);

            // Order restrictions
            $table->decimal('min_order_amount', 10, 3)->nullable(); // Min cart value to use coupon
            $table->decimal('max_discount_amount', 10, 3)->nullable(); // Cap discount (for percentage)

            // Usage limits
            $table->integer('usage_limit')->nullable(); // Total uses allowed (null = unlimited)
            $table->integer('usage_limit_per_user')->nullable(); // Uses per customer (null = unlimited)
            $table->integer('used_count')->default(0); // How many times used

            // Validity period
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_to')->nullable();
            $table->boolean('is_active')->default(true);

            // Product/Category targeting
            $table->string('applies_to')->default('all'); // all, specific_products, specific_categories
            $table->json('applicable_ids')->nullable(); // Product/Category IDs

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('is_active');
            $table->index('valid_from');
            $table->index('valid_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
