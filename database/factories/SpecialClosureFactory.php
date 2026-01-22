<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\SpecialClosure;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SpecialClosure> */
final class SpecialClosureFactory extends Factory
{
    protected $model = SpecialClosure::class;

    public function definition(): array
    {
        return [
            'date' => fake()->dateTimeBetween('now', '+3 months'),
            'reason' => fake()->randomElement([
                'Public Holiday',
                'Maintenance',
                'Staff Training',
                'Private Event',
                'Annual Leave',
            ]),
        ];
    }

    public function eidAlFitr(): static
    {
        return $this->state(fn (array $attributes) => [
            'reason' => 'Eid Al-Fitr Holiday',
        ]);
    }

    public function eidAlAdha(): static
    {
        return $this->state(fn (array $attributes) => [
            'reason' => 'Eid Al-Adha Holiday',
        ]);
    }

    public function nationalDay(): static
    {
        return $this->state(fn (array $attributes) => [
            'date' => now()->setMonth(12)->setDay(16),
            'reason' => 'Bahrain National Day',
        ]);
    }

    public function maintenance(): static
    {
        return $this->state(fn (array $attributes) => [
            'reason' => 'Scheduled Maintenance',
        ]);
    }

    public function past(): static
    {
        return $this->state(fn (array $attributes) => [
            'date' => fake()->dateTimeBetween('-3 months', '-1 day'),
        ]);
    }

    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'date' => fake()->dateTimeBetween('+1 day', '+3 months'),
        ]);
    }

    public function today(): static
    {
        return $this->state(fn (array $attributes) => [
            'date' => now(),
        ]);
    }
}
