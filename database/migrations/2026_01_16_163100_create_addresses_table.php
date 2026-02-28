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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();

            // User relationship
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Address details
            $table->string('label')->nullable(); // "Home", "Work", "Mom's House"
            $table->string('recipient_name');
            $table->string('phone');

            // Bahrain-specific address structure
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('building_number')->nullable();
            $table->string('floor')->nullable();
            $table->string('apartment')->nullable();
            $table->string('area'); // Adliya, Juffair, etc.
            $table->string('block')->nullable(); // Block number
            $table->string('city')->default('Manama');
            $table->string('postal_code')->nullable();
            $table->string('country')->default('Bahrain');

            // Delivery instructions
            $table->text('delivery_instructions')->nullable();

            // Default address flag
            $table->boolean('is_default')->default(false);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('user_id');
            $table->index('area');
            $table->index('is_default');
        });

        // Partial unique index: only one default address per user
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('CREATE UNIQUE INDEX addresses_user_id_is_default_unique ON addresses (user_id) WHERE is_default = true');
        }
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
