<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\BusinessHour;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<BusinessHour> */
final class BusinessHourFactory extends Factory
{
    protected $model = BusinessHour::class;

    public function definition(): array
    {
        return [
            'day_of_week' => fake()->numberBetween(0, 6),
            'opening_time' => '07:00',
            'closing_time' => '22:00',
            'is_closed' => false,
        ];
    }

    public function sunday(): static
    {
        return $this->state(fn (array $attributes) => [
            'day_of_week' => 0,
        ]);
    }

    public function monday(): static
    {
        return $this->state(fn (array $attributes) => [
            'day_of_week' => 1,
        ]);
    }

    public function tuesday(): static
    {
        return $this->state(fn (array $attributes) => [
            'day_of_week' => 2,
        ]);
    }

    public function wednesday(): static
    {
        return $this->state(fn (array $attributes) => [
            'day_of_week' => 3,
        ]);
    }

    public function thursday(): static
    {
        return $this->state(fn (array $attributes) => [
            'day_of_week' => 4,
        ]);
    }

    public function friday(): static
    {
        return $this->state(fn (array $attributes) => [
            'day_of_week' => 5,
            'opening_time' => '14:00',
            'closing_time' => '22:00',
        ]);
    }

    public function saturday(): static
    {
        return $this->state(fn (array $attributes) => [
            'day_of_week' => 6,
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_closed' => true,
            'opening_time' => null,
            'closing_time' => null,
        ]);
    }

    public function morningOnly(): static
    {
        return $this->state(fn (array $attributes) => [
            'opening_time' => '06:00',
            'closing_time' => '14:00',
        ]);
    }

    public function eveningOnly(): static
    {
        return $this->state(fn (array $attributes) => [
            'opening_time' => '16:00',
            'closing_time' => '23:00',
        ]);
    }
}
