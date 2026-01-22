<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Setting> */
final class SettingFactory extends Factory
{
    protected $model = Setting::class;

    public function definition(): array
    {
        $key = fake()->unique()->slug(2);

        return [
            'key' => $key,
            'value' => fake()->word(),
            'type' => 'string',
            'group' => fake()->randomElement(['general', 'shop', 'delivery', 'payment', 'notification']),
            'is_public' => fake()->boolean(30),
            'description' => fake()->optional(0.5)->sentence(),
        ];
    }

    public function storeName(): static
    {
        return $this->state(fn (array $attributes) => [
            'key' => 'store_name',
            'value' => 'Easy Bake',
            'type' => 'string',
            'group' => 'general',
            'is_public' => true,
        ]);
    }

    public function storeEmail(): static
    {
        return $this->state(fn (array $attributes) => [
            'key' => 'store_email',
            'value' => 'hello@easybake.bh',
            'type' => 'string',
            'group' => 'general',
            'is_public' => true,
        ]);
    }

    public function storePhone(): static
    {
        return $this->state(fn (array $attributes) => [
            'key' => 'store_phone',
            'value' => '+973-1234-5678',
            'type' => 'string',
            'group' => 'general',
            'is_public' => true,
        ]);
    }

    public function currency(): static
    {
        return $this->state(fn (array $attributes) => [
            'key' => 'currency',
            'value' => 'BHD',
            'type' => 'string',
            'group' => 'shop',
            'is_public' => true,
        ]);
    }

    public function taxRate(): static
    {
        return $this->state(fn (array $attributes) => [
            'key' => 'tax_rate',
            'value' => '10',
            'type' => 'integer',
            'group' => 'shop',
            'is_public' => true,
        ]);
    }

    public function minOrderAmount(): static
    {
        return $this->state(fn (array $attributes) => [
            'key' => 'min_order_amount',
            'value' => '5.000',
            'type' => 'string',
            'group' => 'shop',
            'is_public' => true,
        ]);
    }

    public function deliveryEnabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'key' => 'delivery_enabled',
            'value' => 'true',
            'type' => 'boolean',
            'group' => 'delivery',
            'is_public' => true,
        ]);
    }

    public function maintenanceMode(): static
    {
        return $this->state(fn (array $attributes) => [
            'key' => 'maintenance_mode',
            'value' => 'false',
            'type' => 'boolean',
            'group' => 'general',
            'is_public' => false,
        ]);
    }

    public function private(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_public' => false,
        ]);
    }

    public function public(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_public' => true,
        ]);
    }

    public function group(string $group): static
    {
        return $this->state(fn (array $attributes) => [
            'group' => $group,
        ]);
    }

    public function boolean(): static
    {
        return $this->state(fn (array $attributes) => [
            'value' => fake()->randomElement(['true', 'false']),
            'type' => 'boolean',
        ]);
    }

    public function integer(): static
    {
        return $this->state(fn (array $attributes) => [
            'value' => (string) fake()->numberBetween(1, 100),
            'type' => 'integer',
        ]);
    }

    public function json(): static
    {
        return $this->state(fn (array $attributes) => [
            'value' => json_encode(['key1' => 'value1', 'key2' => 'value2']),
            'type' => 'json',
        ]);
    }
}
