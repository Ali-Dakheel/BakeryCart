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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->foreignId('shipping_address_id')
                ->nullable()
                ->constrained('addresses')
                ->onDelete('set null');


            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();

            $table->string('shipping_name');
            $table->string('shipping_phone')->nullable();
            $table->string('shipping_address_line_1');
            $table->string('shipping_address_line_2')->nullable();
            $table->string('shipping_building')->nullable();
            $table->string('shipping_floor')->nullable();
            $table->string('shipping_apartment')->nullable();
            $table->string('shipping_area');
            $table->string('shipping_city');
            $table->text('delivery_instructions')->nullable();

            $table->decimal('subtotal', 10, 3); // Before discounts
            $table->decimal('discount_amount', 10, 3)->default(0);
            $table->string('coupon_code')->nullable();
            $table->decimal('coupon_discount', 10, 3)->default(0);
            $table->decimal('tax_percentage', 5, 2)->default(0); // e.g., 10.00 for 10%
            $table->decimal('tax_amount', 10, 3)->default(0);
            $table->decimal('shipping_fee', 10, 3)->default(0);
            $table->decimal('total', 10, 3); // Final amount
            $table->string('currency', 3)->default('BHD');

            $table->string('status')->default('pending');
            $table->string('payment_status')->default('pending');
            $table->string('fulfillment_status')->default('unfulfilled');

            $table->date('delivery_date')->nullable();
            $table->string('delivery_time_slot')->nullable();
            $table->timestamp('delivered_at')->nullable();

            $table->text('customer_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->text('cancellation_reason')->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('source')->default('web'); // web, mobile_app, phone, in_store
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();



            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('customer_email');
            $table->index('status');
            $table->index('payment_status');
            $table->index('delivery_date');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
