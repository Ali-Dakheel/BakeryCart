<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CouponUsage> */
final class CouponUsageFactory extends Factory
{
    protected $model = CouponUsage::class;

    public function definition(): array
    {
        return [
            'coupon_id' => Coupon::factory(),
            'user_id' => User::factory(),
            'order_id' => Order::factory(),
            'discount_amount' => fake()->randomFloat(3, 0.500, 10.000),
            'used_at' => now(),
        ];
    }

    public function recentlyUsed(): static
    {
        return $this->state(fn (array $attributes) => [
            'used_at' => now()->subHours(fake()->numberBetween(1, 48)),
        ]);
    }
}
