<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\TaxRate;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<TaxRate> */
final class TaxRateFactory extends Factory
{
    protected $model = TaxRate::class;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['VAT', 'Sales Tax', 'GST']),
            'rate' => fake()->randomFloat(2, 5.00, 15.00),
            'is_inclusive' => false,
            'applies_to' => 'all',
            'applicable_ids' => null,
            'is_active' => true,
            'effective_from' => null,
            'effective_to' => null,
        ];
    }

    public function bahrain(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'VAT',
            'rate' => 10.00,
            'is_inclusive' => false,
            'applies_to' => 'all',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function inclusive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_inclusive' => true,
        ]);
    }

    public function forProducts(array $productIds): static
    {
        return $this->state(fn (array $attributes) => [
            'applies_to' => 'specific_products',
            'applicable_ids' => $productIds,
        ]);
    }

    public function forCategories(array $categoryIds): static
    {
        return $this->state(fn (array $attributes) => [
            'applies_to' => 'specific_categories',
            'applicable_ids' => $categoryIds,
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'effective_from' => now()->addMonth(),
            'effective_to' => now()->addYear(),
        ]);
    }
}
