<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('cascade');

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            $table->foreignId('order_id')
                ->nullable()
                ->constrained('orders')
                ->onDelete('set null');

            $table->unsignedTinyInteger('rating');
            $table->string('title')->nullable(); // Review headline
            $table->text('comment'); // Review text

            // Verification and moderation
            $table->boolean('is_verified_purchase')->default(false); // Bought the product
            $table->boolean('is_approved')->default(false); // Admin approved

            // Engagement
            $table->integer('helpful_count')->default(0); // "Was this helpful?" votes

            // Admin response
            $table->text('admin_response')->nullable();
            $table->timestamp('responded_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('product_id');
            $table->index('user_id');
            $table->index('is_approved');
            $table->index('rating');
            $table->index('created_at');

            // Composite unique constraint: One review per user per product
            $table->unique(['user_id', 'product_id']);
        });

    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
