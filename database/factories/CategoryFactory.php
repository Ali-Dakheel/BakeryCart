<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /** @var class-string<Model> */
    protected $model = Category::class;
    /** @return array<string, mixed> */
    public function definition(): array
    {
        $name = fake()->randomElement([
            'Bread', 'Pastries', 'Croissants', 'Baguettes',
            'Sourdough', 'Cakes', 'Cookies', 'Danishes',
            'Muffins', 'Scones', 'Tarts', 'Pies'
        ]);
        return [
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(1, 1000),
            'description' => fake()->optional(0.7)->sentence(),
            'image_url' => fake()->optional(0.5)->imageUrl(400, 300, 'food'),
            'icon' => fake()->optional(0.3)->randomElement(['🍞', '🥐', '🧁', '🥖', '🍰']),
            'sort_order' => fake()->numberBetween(0, 100),
            'is_active' => fake()->boolean(90),
            'meta_title' => fake()->optional(0.5)->sentence(3),
            'meta_description' => fake()->optional(0.5)->sentence(10),
        ];
    }
    public function withTranslations(): static
    {
        return $this->afterCreating(function (Category $category) {
            // English translation
            $category->translations()->create([
                'locale' => 'en',
                'name' => fake()->randomElement([
                    'Bread', 'Pastries', 'Croissants', 'Baguettes',
                    'Sourdough', 'Cakes', 'Cookies', 'Danishes'
                ]),
                'description' => fake()->sentence(),
            ]);

            // Arabic translation
            $category->translations()->create([
                'locale' => 'ar',
                'name' => fake()->randomElement([
                    'خبز', 'معجنات', 'كرواسون', 'باجيت',
                    'عجين مخمر', 'كيك', 'بسكويت', 'دانش'
                ]),
                'description' => fake()->sentence(),
            ]);
        });
    }
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function child(Category $parent): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parent->id,
        ]);
    }


}
