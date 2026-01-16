<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\CategoryTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CategoryTranslation>
 */
class CategoryTranslationFactory extends Factory
{
    /** @var class-string<Model> */
    protected $model = CategoryTranslation::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'locale' => 'en',
            'name' => fake()->randomElement([
                'Bread', 'Pastries', 'Croissants', 'Baguettes',
                'Sourdough', 'Cakes', 'Cookies', 'Danishes'
            ]),
            'description' => fake()->optional(0.7)->sentence(),
        ];
    }
    public function english(): static
    {
        return $this->state(fn (array $attributes) => [
            'locale' => 'en',
        ]);
    }

    /**
     * Create Arabic translation
     */
    public function arabic(): static
    {
        return $this->state(fn (array $attributes) => [
            'locale' => 'ar',
            'name' => fake()->randomElement([
                'خبز', 'معجنات', 'كرواسون', 'باجيت',
                'عجين مخمر', 'كيك', 'بسكويت', 'دانش'
            ]),
        ]);
    }

}
