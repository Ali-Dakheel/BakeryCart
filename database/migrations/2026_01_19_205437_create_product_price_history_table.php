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
        Schema::create('product_price_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('cascade');

            $table->foreignId('product_variant_id')
                ->nullable()
                ->constrained('product_variants')
                ->onDelete('cascade');

            // Price changes
            $table->decimal('old_price', 10, 3);
            $table->decimal('new_price', 10, 3);

            // Who changed it
            $table->foreignId('changed_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            $table->string('reason')->nullable(); // "Sale", "Cost increase", "Promotion"

            $table->timestamp('changed_at')->useCurrent();

            // Indexes
            $table->index('product_id');
            $table->index('product_variant_id');
            $table->index('changed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_price_history');
    }
};
