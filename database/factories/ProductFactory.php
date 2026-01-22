<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;


final class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->randomElement([
            'Butter Croissant',
            'Pain au Chocolat',
            'Classic Baguette',
            'Sourdough Loaf',
            'Brioche Bun',
            'Cinnamon Danish',
            'Almond Croissant',
            'Focaccia Bread',
            'Cheese Danish',
            'Rye Bread',
        ]);
        return [
            'category_id' => Category::factory(),  // Creates category if needed
            'sku' => 'PROD-' . fake()->unique()->numberBetween(1000, 9999),
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(1, 1000),
            'price' => fake()->randomFloat(3, 0.250, 15.000),  // BHD
            'compare_at_price' => null,
            'cost' => fake()->randomFloat(3, 0.100, 10.000),
            'current_stock' => fake()->numberBetween(0, 100),
            'low_stock_threshold' => 10,
            'daily_production_capacity' => fake()->numberBetween(20, 200),
            'lead_time_hours' => fake()->randomElement([0, 2, 4, 24]),
            'is_available' => true,
            'is_featured' => fake()->boolean(20),  // 20% featured
            'available_from' => null,
            'available_until' => null,
            'views_count' => fake()->numberBetween(0, 1000),
            'sales_count' => fake()->numberBetween(0, 500),
        ];
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_quantity' => 0,
            'is_available' => false,
        ]);
    }

    public function onSale(): static
    {
        return $this->state(fn (array $attributes) => [
            // Use $attributes['base_price'] from definition()
            'sale_price' => round($attributes['base_price'] * 0.8, 3),  // 20% off
        ]);
    }

    public function unavailable(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_available' => false,
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'available_from' => now()->addDays(1),
            'available_until' => now()->addDays(7),
        ]);
    }

    public function lowStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_quantity' => fake()->numberBetween(1, 5),
            'low_stock_threshold' => 10,
        ]);
    }

    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'sales_count' => fake()->numberBetween(500, 2000),
            'views_count' => fake()->numberBetween(2000, 10000),
        ]);
    }
    public function withTranslations(): static
    {
        return $this->afterCreating(function (Product $product) {
            // English translation
            $product->translations()->create([
                'locale' => 'en',
                'name' => fake()->randomElement([
                    'Butter Croissant', 'Chocolate Croissant', 'French Baguette',
                    'Sourdough Bread', 'Cinnamon Roll', 'Almond Danish',
                ]),
                'short_description' => fake()->sentence(),
                'description' => fake()->paragraphs(2, true),
            ]);

            $product->translations()->create([
                'locale' => 'ar',
                'name' => fake()->randomElement([
                    'كرواسون بالزبدة', 'كرواسون بالشوكولاتة', 'باجيت فرنسي',
                    'خبز العجين المخمر', 'لفائف القرفة', 'دانش باللوز',
                ]),
                'short_description' => fake()->sentence(),
                'description' => fake()->paragraphs(2, true),
            ]);
        });
    }

    public function withImages(int $count = 3): static
    {
        return $this->afterCreating(function (Product $product) use ($count) {
            for ($i = 0; $i < $count; $i++) {
                $product->images()->create([
                    'image_url' => fake()->imageUrl(800, 600, 'food'),
                    'alt_text' => "Product image " . ($i + 1),
                    'is_primary' => $i === 0,  // First image is primary
                    'sort_order' => $i,
                ]);
            }
        });
    }

    public function withVariants(): static
    {
        return $this->afterCreating(function (Product $product) {
            $basePrice = (float) $product->base_price;

            $product->variants()->createMany([
                [
                    'sku' => $product->sku . '-1PC',
                    'name' => 'Single',
                    'pack_quantity' => 1,
                    'price' => $basePrice,
                    'stock_quantity' => fake()->numberBetween(10, 50),
                    'is_available' => true,
                ],
                [
                    'sku' => $product->sku . '-6PK',
                    'name' => '6-Pack',
                    'pack_quantity' => 6,
                    'price' => round($basePrice * 5.5, 3),  // Slight discount
                    'stock_quantity' => fake()->numberBetween(5, 20),
                    'is_available' => true,
                ],
                [
                    'sku' => $product->sku . '-12PK',
                    'name' => '12-Pack (Box)',
                    'pack_quantity' => 12,
                    'price' => round($basePrice * 10, 3),  // Better discount
                    'stock_quantity' => fake()->numberBetween(2, 10),
                    'is_available' => true,
                ],
            ]);
        });
    }

    public function complete(): static
    {
        return $this
            ->withTranslations()
            ->withImages(3)
            ->withVariants();
    }

}
