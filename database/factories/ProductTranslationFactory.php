<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ProductTranslation> */
final class ProductTranslationFactory extends Factory
{
    protected $model = ProductTranslation::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'locale' => 'en',
            'name' => fake()->randomElement([
                'Butter Croissant', 'Pain au Chocolat', 'Classic Baguette',
                'Sourdough Loaf', 'Brioche Bun', 'Cinnamon Danish',
                'Almond Croissant', 'Focaccia Bread', 'Cheese Danish',
            ]),
            'description' => fake()->paragraphs(2, true),
            'short_description' => fake()->sentence(),
            'meta_title' => fake()->optional(0.5)->sentence(4),
            'meta_description' => fake()->optional(0.5)->sentence(),
        ];
    }

    public function english(): static
    {
        return $this->state(fn (array $attributes) => [
            'locale' => 'en',
            'name' => fake()->randomElement([
                'Butter Croissant', 'Pain au Chocolat', 'Classic Baguette',
                'Sourdough Loaf', 'Brioche Bun', 'Cinnamon Danish',
            ]),
        ]);
    }

    public function arabic(): static
    {
        return $this->state(fn (array $attributes) => [
            'locale' => 'ar',
            'name' => fake()->randomElement([
                'كرواسون بالزبدة', 'كرواسون بالشوكولاتة', 'باجيت فرنسي',
                'خبز العجين المخمر', 'بريوش', 'دانش بالقرفة',
                'كرواسون باللوز', 'فوكاتشا', 'دانش بالجبن',
            ]),
        ]);
    }
}
