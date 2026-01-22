<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

final class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $coupons = [
            // Welcome coupon for new customers
            [
                'code' => 'WELCOME10',
                'type' => 'percentage',
                'value' => 10.000,
                'min_order_amount' => 5.000,
                'max_discount_amount' => 5.000,
                'usage_limit' => null,
                'usage_limit_per_user' => 1,
                'used_count' => 0,
                'valid_from' => now(),
                'valid_to' => now()->addYear(),
                'is_active' => true,
                'applies_to' => 'all',
                'applicable_ids' => null,
            ],
            // Flat discount
            [
                'code' => 'SAVE2',
                'type' => 'fixed',
                'value' => 2.000,
                'min_order_amount' => 10.000,
                'max_discount_amount' => null,
                'usage_limit' => 500,
                'usage_limit_per_user' => 3,
                'used_count' => 0,
                'valid_from' => now(),
                'valid_to' => now()->addMonths(6),
                'is_active' => true,
                'applies_to' => 'all',
                'applicable_ids' => null,
            ],
            // Free shipping
            [
                'code' => 'FREESHIP',
                'type' => 'free_shipping',
                'value' => 0,
                'min_order_amount' => 15.000,
                'max_discount_amount' => null,
                'usage_limit' => 200,
                'usage_limit_per_user' => 2,
                'used_count' => 0,
                'valid_from' => now(),
                'valid_to' => now()->addMonths(3),
                'is_active' => true,
                'applies_to' => 'all',
                'applicable_ids' => null,
            ],
            // Big saver - 20% off
            [
                'code' => 'BIGSAVE20',
                'type' => 'percentage',
                'value' => 20.000,
                'min_order_amount' => 25.000,
                'max_discount_amount' => 10.000,
                'usage_limit' => 100,
                'usage_limit_per_user' => 1,
                'used_count' => 0,
                'valid_from' => now(),
                'valid_to' => now()->addMonths(2),
                'is_active' => true,
                'applies_to' => 'all',
                'applicable_ids' => null,
            ],
            // Ramadan special (inactive for now)
            [
                'code' => 'RAMADAN25',
                'type' => 'percentage',
                'value' => 25.000,
                'min_order_amount' => 20.000,
                'max_discount_amount' => 15.000,
                'usage_limit' => 1000,
                'usage_limit_per_user' => 5,
                'used_count' => 0,
                'valid_from' => now()->addMonths(2),
                'valid_to' => now()->addMonths(3),
                'is_active' => false,
                'applies_to' => 'all',
                'applicable_ids' => null,
            ],
            // VIP coupon
            [
                'code' => 'VIP50',
                'type' => 'percentage',
                'value' => 50.000,
                'min_order_amount' => 50.000,
                'max_discount_amount' => 25.000,
                'usage_limit' => 10,
                'usage_limit_per_user' => 1,
                'used_count' => 0,
                'valid_from' => now(),
                'valid_to' => now()->addMonths(12),
                'is_active' => true,
                'applies_to' => 'all',
                'applicable_ids' => null,
            ],
        ];

        foreach ($coupons as $coupon) {
            Coupon::create($coupon);
        }

        $this->command->info('Coupons seeded: ' . count($coupons) . ' discount codes');
    }
}
