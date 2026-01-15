<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /** @var class-string<Model> */
    protected $model = User::class;
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'phone'=> fake()->optional(0.7)->numerify('+973-####-####'),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /** @return User<Model> */
    public function customer(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('customer');
        });
    }

    public function staff(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => fake()->name() . ' (Staff)',
            'email' => 'staff.' . fake()->unique()->userName() . '@easybake.bh',
        ])->afterCreating(function (User $user) {
            $user->assignRole('staff');
        });
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => fake()->name() . ' (Admin)',
            'email' => 'admin.' . fake()->unique()->userName() . '@easybake.bh',
        ])->afterCreating(function (User $user) {
            $user->assignRole('admin');
        });
    }

    public function withoutPhone(): static
    {
        return $this->state(fn (array $attributes) => [
            'phone' => null,
        ]);
    }

}
