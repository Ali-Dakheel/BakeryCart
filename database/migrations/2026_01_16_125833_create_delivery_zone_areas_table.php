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
        Schema::create('delivery_zone_areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_zone_id')
                ->constrained('delivery_zones')
                ->onDelete('cascade');
            $table->string('area_name');
            $table->timestamps();

            $table->index('delivery_zone_id');
            $table->index('area_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_zone_areas');
    }
};
