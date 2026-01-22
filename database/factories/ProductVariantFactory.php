<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ProductVariant> */
final class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    public function definition(): array
    {
        $packQuantity = fake()->randomElement([1, 6, 12]);
        $basePrice = fake()->randomFloat(3, 0.500, 5.000);

        return [
            'product_id' => Product::factory(),
            'name' => match($packQuantity) {
                1 => 'Single',
                6 => '6-Pack',
                12 => '12-Pack (Box)',
                default => 'Single',
            },
            'sku' => 'VAR-' . fake()->unique()->numberBetween(1000, 9999),
            'price' => round($basePrice * $packQuantity * 0.95, 3),
            'stock' => fake()->numberBetween(0, 50),
            'pack_quantity' => $packQuantity,
            'weight_grams' => $packQuantity * fake()->numberBetween(50, 200),
            'is_available' => true,
            'sort_order' => 0,
        ];
    }

    public function single(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Single',
            'pack_quantity' => 1,
            'sort_order' => 0,
        ]);
    }

    public function sixPack(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => '6-Pack',
            'pack_quantity' => 6,
            'sort_order' => 1,
        ]);
    }

    public function twelvePack(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => '12-Pack (Box)',
            'pack_quantity' => 12,
            'sort_order' => 2,
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
            'is_available' => false,
        ]);
    }

    public function unavailable(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_available' => false,
        ]);
    }
}
