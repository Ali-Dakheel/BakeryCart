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
        Schema::create('order_cancellations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->unique() // Each order can only have ONE cancellation record
                ->constrained('orders')
                ->onDelete('cascade');

            // Who cancelled the order
            $table->string('cancelled_by'); // customer, admin, system

            // Cancellation details
            $table->text('cancellation_reason');
            $table->decimal('refund_amount', 10, 3)->default(0);

            // Refund tracking
            $table->string('refund_status')->default('pending');
            // pending, processing, completed, failed

            $table->string('refund_method')->nullable();
            // original_payment, store_credit, bank_transfer

            $table->string('refund_transaction_id')->nullable(); // Payment gateway transaction ID

            $table->timestamp('cancelled_at')->useCurrent();
            $table->timestamp('refunded_at')->nullable();

            $table->timestamps();

            $table->index('refund_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_cancellations');
    }
};
