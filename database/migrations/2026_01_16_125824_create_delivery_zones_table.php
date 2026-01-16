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
        Schema::create('delivery_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('base_fee', 10, 3);
            $table->decimal('free_delivery_threshold', 10, 3)->nullable();
            $table->string('estimated_delivery_time')->nullable();
            $table->boolean('is_active')->default(true);
             $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_zones');
    }
};
