<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Coupon> */
final class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition(): array
    {
        return [
            'code' => Str::upper(fake()->unique()->bothify('???##')),
            'type' => fake()->randomElement(['percentage', 'fixed', 'free_shipping']),
            'value' => fake()->randomFloat(3, 1.000, 20.000),
            'min_order_amount' => fake()->optional(0.7)->randomFloat(3, 5.000, 50.000),
            'max_discount_amount' => fake()->optional(0.5)->randomFloat(3, 5.000, 25.000),
            'usage_limit' => fake()->optional(0.6)->numberBetween(50, 500),
            'usage_limit_per_user' => fake()->optional(0.7)->numberBetween(1, 5),
            'used_count' => 0,
            'valid_from' => now(),
            'valid_to' => now()->addMonths(fake()->numberBetween(1, 6)),
            'is_active' => true,
            'applies_to' => fake()->randomElement(['all', 'specific_products', 'specific_categories']),
            'applicable_ids' => null,
        ];
    }

    public function percentage(float $percent = 10.0): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'percentage',
            'value' => $percent,
        ]);
    }

    public function fixed(float $amount = 2.000): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'fixed',
            'value' => $amount,
        ]);
    }

    public function freeShipping(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'free_shipping',
            'value' => 0,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'valid_from' => now()->subMonths(2),
            'valid_to' => now()->subMonth(),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function unlimited(): static
    {
        return $this->state(fn (array $attributes) => [
            'usage_limit' => null,
            'usage_limit_per_user' => null,
        ]);
    }

    public function singleUse(): static
    {
        return $this->state(fn (array $attributes) => [
            'usage_limit' => 1,
            'usage_limit_per_user' => 1,
        ]);
    }

    public function welcome(): static
    {
        return $this->state(fn (array $attributes) => [
            'code' => 'WELCOME10',
            'type' => 'percentage',
            'value' => 10.000,
            'usage_limit_per_user' => 1,
            'min_order_amount' => 5.000,
        ]);
    }
}
