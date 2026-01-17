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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('cascade');

            $table->string('name'); // "Single", "Pack of 6", "Pack of 12"
            $table->string('sku')->unique();

            $table->decimal('price', 10, 3);

            $table->integer('stock')->default(0);
            $table->integer('pack_quantity')->default(1);

            $table->integer('weight_grams')->nullable();

            $table->boolean('is_available')->default(true);
            $table->integer('sort_order')->default(0);

            $table->timestamps();


            $table->index('product_id');
            $table->index('is_available');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
