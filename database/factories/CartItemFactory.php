<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CartItem> */
final class CartItemFactory extends Factory
{
    protected $model = CartItem::class;

    public function definition(): array
    {
        return [
            'cart_id' => Cart::factory(),
            'product_id' => Product::factory(),
            'product_variant_id' => null,
            'quantity' => fake()->numberBetween(1, 5),
            'price' => fake()->randomFloat(3, 0.500, 15.000),
        ];
    }

    public function withVariant(): static
    {
        return $this->state(function (array $attributes) {
            $variant = ProductVariant::factory()->create([
                'product_id' => $attributes['product_id'],
            ]);

            return [
                'product_variant_id' => $variant->id,
                'price' => $variant->price,
            ];
        });
    }

    public function quantity(int $qty): static
    {
        return $this->state(fn (array $attributes) => [
            'quantity' => $qty,
        ]);
    }
}
