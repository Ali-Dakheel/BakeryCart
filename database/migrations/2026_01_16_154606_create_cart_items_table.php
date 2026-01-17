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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')
                ->constrained('carts')
                ->onDelete('cascade');

            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('cascade');

            // Foreign key to product variants (nullable if buying base product)
            $table->foreignId('product_variant_id')
                ->nullable()
                ->constrained('product_variants')
                ->onDelete('cascade');

            // Quantity
            $table->integer('quantity')->default(1);

            // Price snapshot (price at time of adding to cart)
            $table->decimal('price', 10, 3);

            $table->timestamps();

            // Indexes
            $table->index('cart_id');
            $table->index('product_id');

            $table->unique(['cart_id', 'product_id', 'product_variant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
