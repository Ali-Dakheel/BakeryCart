<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductPriceHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ProductPriceHistory> */
final class ProductPriceHistoryFactory extends Factory
{
    protected $model = ProductPriceHistory::class;

    public function definition(): array
    {
        $oldPrice = fake()->randomFloat(3, 1.000, 10.000);
        $priceChange = fake()->randomElement([-0.500, -0.250, 0.250, 0.500, 1.000]);
        $newPrice = max(0.100, $oldPrice + $priceChange);

        return [
            'product_id' => Product::factory(),
            'product_variant_id' => null,
            'old_price' => round($oldPrice, 3),
            'new_price' => round($newPrice, 3),
            'changed_by' => User::factory(),
            'reason' => fake()->optional(0.7)->randomElement([
                'Price adjustment',
                'Seasonal discount',
                'Cost increase',
                'Promotion',
                'Market adjustment',
            ]),
            'changed_at' => now()->subDays(fake()->numberBetween(1, 30)),
        ];
    }

    public function priceIncrease(): static
    {
        return $this->state(function (array $attributes) {
            $oldPrice = (float) $attributes['old_price'];
            return [
                'new_price' => round($oldPrice * 1.1, 3),
                'reason' => 'Cost increase',
            ];
        });
    }

    public function priceDecrease(): static
    {
        return $this->state(function (array $attributes) {
            $oldPrice = (float) $attributes['old_price'];
            return [
                'new_price' => round($oldPrice * 0.9, 3),
                'reason' => 'Promotion',
            ];
        });
    }

    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'changed_at' => now()->subHours(fake()->numberBetween(1, 48)),
        ]);
    }
}
