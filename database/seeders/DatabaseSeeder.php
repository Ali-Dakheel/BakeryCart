<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Core configuration (no dependencies)
        $this->call([
            RolePermissionSeeder::class,
            TaxRateSeeder::class,
            SettingSeeder::class,
            BusinessHourSeeder::class,
        ]);

        // 2. Delivery zones with areas
        $this->call(DeliveryZoneSeeder::class);

        // 3. Categories
        $this->call(CategorySeeder::class);

        // 4. Products (needs categories)
        $this->call(ProductSeeder::class);

        // 5. Users
        User::factory()->admin()->create([
            'name' => 'EasyBake Admin',
            'email' => 'admin@easybake.bh',
            'phone' => '+973-3900-0000',
        ]);

        User::factory()->staff()->count(2)->create();
        User::factory()->customer()->count(10)->create();
        User::factory()->customer()->unverified()->count(5)->create();

        // 6. Dev/Staging only: Demo data (coupons, orders, reviews)
        if (app()->environment('local', 'staging')) {
            $this->call([
                CouponSeeder::class,
                DemoDataSeeder::class,
            ]);
        }
    }
}
