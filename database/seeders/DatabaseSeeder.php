<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([RolePermissionSeeder::class,
            CategorySeeder::class
        ]);

        User::factory()->admin()->create([
            'name' => 'BakeryCart Admin',
            'email' => 'admin@easybake.bh',
            'phone' => '+973-3900-0000',
        ]);

        // Create staff users for testing
        User::factory()->staff()->count(2)->create();

        // Create customer users for testing
        User::factory()->customer()->count(10)->create();

        // Create some unverified customers
        User::factory()->customer()->unverified()->count(5)->create();

    }
}
