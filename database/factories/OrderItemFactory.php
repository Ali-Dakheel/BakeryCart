<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<OrderItem> */
final class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $unitPrice = fake()->randomFloat(3, 0.500, 15.000);
        $quantity = fake()->numberBetween(1, 5);
        $discountAmount = 0;
        $taxAmount = round($unitPrice * $quantity * 0.10, 3);
        $total = round(($unitPrice * $quantity) - $discountAmount + $taxAmount, 3);

        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'product_variant_id' => null,
            'product_name' => fake()->randomElement([
                'Butter Croissant', 'Pain au Chocolat', 'Baguette',
                'Sourdough Loaf', 'Cinnamon Danish', 'Almond Croissant',
            ]),
            'product_sku' => 'PROD-' . fake()->unique()->numberBetween(1000, 9999),
            'product_description' => fake()->optional(0.5)->sentence(),
            'variant_name' => null,
            'variant_attributes' => null,
            'image_url' => fake()->optional(0.7)->imageUrl(400, 400, 'food'),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'discount_amount' => $discountAmount,
            'tax_amount' => $taxAmount,
            'total' => $total,
            'special_instructions' => fake()->optional(0.2)->sentence(),
        ];
    }

    public function withVariant(string $name = '6-Pack'): static
    {
        return $this->state(fn (array $attributes) => [
            'variant_name' => $name,
            'variant_attributes' => ['pack_quantity' => 6],
        ]);
    }

    public function withDiscount(float $amount = 0.500): static
    {
        return $this->state(function (array $attributes) use ($amount) {
            $subtotal = $attributes['unit_price'] * $attributes['quantity'];
            $newTotal = round($subtotal - $amount + $attributes['tax_amount'], 3);

            return [
                'discount_amount' => $amount,
                'total' => $newTotal,
            ];
        });
    }
}
