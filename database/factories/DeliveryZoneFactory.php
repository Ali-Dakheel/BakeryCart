<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\DeliveryZone;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<DeliveryZone> */
final class DeliveryZoneFactory extends Factory
{
    protected $model = DeliveryZone::class;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Central Bahrain', 'North Bahrain', 'South Bahrain', 'Muharraq Island']),
            'base_fee' => fake()->randomFloat(3, 0.500, 2.000),
            'free_delivery_threshold' => fake()->optional(0.6)->randomFloat(3, 15.000, 30.000),
            'estimated_delivery_time' => fake()->randomElement(['30-45 min', '45-60 min', '60-90 min']),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function freeDelivery(): static
    {
        return $this->state(fn (array $attributes) => [
            'base_fee' => 0,
            'free_delivery_threshold' => null,
        ]);
    }

    public function central(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Central Bahrain',
            'base_fee' => 1.000,
            'estimated_delivery_time' => '30-45 min',
            'sort_order' => 1,
        ]);
    }

    public function withAreas(array $areas = null): static
    {
        $areas = $areas ?? ['Manama', 'Juffair', 'Seef', 'Adliya', 'Hoora'];

        return $this->afterCreating(function (DeliveryZone $zone) use ($areas) {
            foreach ($areas as $area) {
                $zone->areas()->create(['area_name' => $area]);
            }
        });
    }
}
