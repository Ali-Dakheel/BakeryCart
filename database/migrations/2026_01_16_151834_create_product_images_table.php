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
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('cascade');

            // Image details
            $table->string('image_url');
            $table->string('alt_text')->nullable(); // For accessibility and SEO
            $table->integer('sort_order')->default(0); // Display order in gallery
            $table->boolean('is_primary')->default(false); // Main product image

            $table->timestamps();

            // Indexes
            $table->index('product_id');
            $table->index('is_primary');
        });
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('CREATE UNIQUE INDEX product_images_product_id_is_primary_unique ON product_images (product_id) WHERE is_primary = true');
        }
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
