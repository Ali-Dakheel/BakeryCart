<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Address> */
final class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'label' => fake()->randomElement(['Home', 'Work', 'Office', 'Other']),
            'recipient_name' => fake()->name(),
            'phone' => fake()->numerify('+973-####-####'),
            'address_line_1' => fake()->streetAddress(),
            'address_line_2' => fake()->optional(0.3)->secondaryAddress(),
            'building_number' => fake()->optional(0.5)->buildingNumber(),
            'floor' => fake()->optional(0.4)->randomElement(['GF', '1', '2', '3', '4', '5']),
            'apartment' => fake()->optional(0.4)->numerify('##'),
            'area' => fake()->randomElement([
                'Manama', 'Muharraq', 'Riffa', 'Hamad Town',
                'Isa Town', 'Saar', 'Budaiya', 'Juffair',
                'Seef', 'Adliya', 'Zinj', 'Hoora',
                'Amwaj Islands', 'Tubli', 'Sanabis',
            ]),
            'block' => fake()->optional(0.6)->numerify('###'),
            'city' => 'Bahrain',
            'postal_code' => fake()->optional(0.3)->numerify('####'),
            'country' => 'BH',
            'delivery_instructions' => fake()->optional(0.3)->sentence(),
            'is_default' => false,
        ];
    }

    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }

    public function home(): static
    {
        return $this->state(fn (array $attributes) => [
            'label' => 'Home',
        ]);
    }

    public function work(): static
    {
        return $this->state(fn (array $attributes) => [
            'label' => 'Work',
        ]);
    }
}
