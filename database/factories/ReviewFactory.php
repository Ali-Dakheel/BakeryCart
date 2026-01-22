<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Review> */
final class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'user_id' => User::factory(),
            'order_id' => null,
            'rating' => fake()->numberBetween(1, 5),
            'title' => fake()->optional(0.7)->sentence(4),
            'comment' => fake()->optional(0.8)->paragraph(),
            'is_verified_purchase' => false,
            'is_approved' => true,
            'helpful_count' => fake()->numberBetween(0, 50),
            'admin_response' => null,
            'responded_at' => null,
        ];
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified_purchase' => true,
            'order_id' => Order::factory(),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved' => false,
        ]);
    }

    public function withAdminResponse(): static
    {
        return $this->state(fn (array $attributes) => [
            'admin_response' => fake()->paragraph(),
            'responded_at' => now(),
        ]);
    }

    public function fiveStars(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => 5,
        ]);
    }

    public function oneStar(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => 1,
        ]);
    }

    public function helpful(): static
    {
        return $this->state(fn (array $attributes) => [
            'helpful_count' => fake()->numberBetween(50, 200),
        ]);
    }

    public function withImages(int $count = 2): static
    {
        return $this->afterCreating(function (Review $review) use ($count) {
            for ($i = 0; $i < $count; $i++) {
                $review->images()->create([
                    'image_url' => fake()->imageUrl(600, 600, 'food'),
                    'sort_order' => $i,
                ]);
            }
        });
    }
}
