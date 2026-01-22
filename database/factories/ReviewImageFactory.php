<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Review;
use App\Models\ReviewImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ReviewImage> */
final class ReviewImageFactory extends Factory
{
    protected $model = ReviewImage::class;

    public function definition(): array
    {
        return [
            'review_id' => Review::factory(),
            'image_url' => fake()->imageUrl(600, 600, 'food'),
            'sort_order' => 0,
        ];
    }

    public function sortOrder(int $order): static
    {
        return $this->state(fn (array $attributes) => [
            'sort_order' => $order,
        ]);
    }
}
