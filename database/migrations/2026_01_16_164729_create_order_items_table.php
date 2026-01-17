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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained('orders')
                ->onDelete('cascade');

            // Product references (for analytics, reports)
            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('restrict');

            $table->foreignId('product_variant_id')
                ->nullable()
                ->constrained('product_variants')
                ->onDelete('restrict');

            // Product snapshot at order time
            $table->string('product_name');
            $table->string('product_sku');
            $table->text('product_description')->nullable();

            // Variant snapshot (if applicable)
            $table->string('variant_name')->nullable(); // "Pack of 6"
            $table->json('variant_attributes')->nullable(); // Pack size, weight, etc.

            $table->string('image_url')->nullable();

            // Pricing breakdown per item
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 3); // Price per unit
            $table->decimal('discount_amount', 10, 3)->default(0); // Discount per item
            $table->decimal('tax_amount', 10, 3)->default(0); // Tax per item
            $table->decimal('total', 10, 3); // Total for this line item

            // Special requests
            $table->text('special_instructions')->nullable();

            $table->timestamps();

            $table->index('order_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
