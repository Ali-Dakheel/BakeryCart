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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                ->constrained('categories')
                ->onDelete('restrict');

            $table->string('slug')->unique();
            $table->string('sku')->unique();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();

            // Pricing (BHD uses 3 decimals: 0.500, 1.250)
            $table->decimal('price', 10, 3);
            $table->decimal('compare_at_price', 10, 3)->nullable(); // Original price for "sale" display
            $table->decimal('cost', 10, 3)->nullable(); // Your cost (for profit calculations)

            // Inventory
            $table->integer('current_stock')->default(0);
            $table->integer('low_stock_threshold')->default(10);
            $table->boolean('track_inventory')->default(true);

            // Bakery-specific fields
            $table->integer('daily_production_capacity')->nullable(); // Max units per day
            $table->integer('lead_time_hours')->nullable(); // Hours needed to prepare
            $table->integer('preparation_time_minutes')->nullable(); // Time to bake/prepare

            // Daily availability window
            $table->time('available_from_time')->nullable(); // e.g., 06:00
            $table->time('available_to_time')->nullable(); // e.g., 14:00

            // Product details
            $table->integer('weight')->nullable(); // Weight in grams
            $table->json('attributes')->nullable(); // Allergens, ingredients, nutritional info

            // Status flags
            $table->boolean('is_available')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_taxable')->default(true);
            $table->boolean('requires_shipping')->default(true);

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('og_image_url')->nullable(); // Open Graph image for social sharing

            // Scheduling (future launches, seasonal products)
            $table->timestamp('available_from')->nullable(); // Start selling date
            $table->timestamp('available_until')->nullable(); // Stop selling date

            // Analytics
            $table->unsignedBigInteger('views_count')->default(0);
            $table->unsignedBigInteger('sales_count')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Indexes for query performance
            $table->index('category_id');
            $table->index('is_available');
            $table->index('is_featured');
            $table->index('sales_count'); // Order by popularity
            $table->index('created_at'); // Order by newest
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
