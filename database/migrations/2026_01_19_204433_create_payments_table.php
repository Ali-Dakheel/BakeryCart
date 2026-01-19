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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained('orders')
                ->onDelete('cascade');

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            // Payment details
            $table->string('payment_method'); // cash, card, benefit_pay, bank_transfer
            $table->decimal('amount', 10, 3);
            $table->string('currency', 3)->default('BHD');
            $table->string('status')->default('pending'); // pending, processing, completed, failed, refunded

            // Gateway details (for future integration)
            $table->string('gateway')->nullable(); // stripe, tap, benefit
            $table->string('transaction_id')->nullable()->unique();
            $table->string('gateway_response')->nullable(); // Success/failure message
            $table->json('gateway_data')->nullable(); // Full gateway response

            // Mock payment (for testing)
            $table->boolean('is_mock')->default(true);

            // Timestamps
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('refunded_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('order_id');
            $table->index('user_id');
            $table->index('status');
            $table->index('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
