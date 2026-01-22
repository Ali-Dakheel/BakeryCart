<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;


final class OrderFactory extends Factory
{

    public function definition(): array
    {
        $subtotal = fake()->randomFloat(3, 5.000, 100.000);
        $taxPercentage = 10.00;
        $taxAmount = round($subtotal * ($taxPercentage / 100), 3);
        $shippingFee = 1.000;
        $total = round($subtotal + $taxAmount + $shippingFee, 3);

        return [
            'order_number' => 'ORD-' . date('Y') . '-' . fake()->unique()->numberBetween(10000, 99999),
            'user_id' => User::factory(),
            'shipping_address_id' => null,

            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'customer_phone' => fake()->numerify('+973-####-####'),

            'shipping_name' => fake()->name(),
            'shipping_phone' => fake()->numerify('+973-####-####'),
            'shipping_address_line_1' => fake()->streetAddress(),
            'shipping_address_line_2' => null,
            'shipping_building' => fake()->optional(0.5)->buildingNumber(),
            'shipping_floor' => fake()->optional(0.3)->randomElement(['1', '2', '3', 'GF']),
            'shipping_apartment' => fake()->optional(0.3)->numerify('##'),
            'shipping_area' => fake()->randomElement([
                'Manama', 'Muharraq', 'Riffa', 'Hamad Town',
                'Isa Town', 'Saar', 'Budaiya', 'Juffair',
                'Seef', 'Adliya', 'Zinj', 'Hoora',
            ]),
            'shipping_city' => 'Bahrain',
            'delivery_instructions' => fake()->optional(0.3)->sentence(),

            'subtotal' => $subtotal,
            'discount_amount' => 0,
            'coupon_code' => null,
            'coupon_discount' => 0,
            'tax_percentage' => $taxPercentage,
            'tax_amount' => $taxAmount,
            'shipping_fee' => $shippingFee,
            'total' => $total,
            'currency' => 'BHD',

            'status' => 'pending',
            'payment_status' => 'pending',
            'fulfillment_status' => 'unfulfilled',

            'delivery_date' => null,
            'delivery_time_slot' => null,
            'delivered_at' => null,

            'customer_notes' => fake()->optional(0.2)->sentence(),
            'admin_notes' => null,
            'cancellation_reason' => null,

            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'source' => fake()->randomElement(['web', 'mobile_app']),
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);
    }

    public function delivered(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'delivered',
            'payment_status' => 'paid',
            'fulfillment_status' => 'fulfilled',
            'delivered_at' => now(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'cancelled',
            'cancellation_reason' => fake()->sentence(),
        ]);
    }

    public function guest(): static
    {
        return $this->state(fn(array $attributes) => [
            'user_id' => null,
        ]);
    }

    public function withCoupon(string $code = 'WELCOME10', float $discount = 1.000): static
    {
        return $this->state(fn(array $attributes) => [
            'coupon_code' => $code,
            'coupon_discount' => $discount,
            'total' => round((float)$attributes['total'] - $discount, 3),
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn(array $attributes) => [
            'delivery_date' => now()->addDays(fake()->numberBetween(1, 3))->format('Y-m-d'),
            'delivery_time_slot' => fake()->randomElement([
                '09:00-12:00', '12:00-15:00', '15:00-18:00',
            ]),
        ]);
    }

    public function refunded(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'cancelled',
            'payment_status' => 'refunded',
            'cancellation_reason' => 'Customer requested refund',
        ]);
    }

    public function withStatusHistory(): static
    {
        return $this->afterCreating(function (Order $order) {
            $order->statusHistories()->create([
                'status' => $order->status,
                'notes' => 'Order created',
                'changed_by' => null,
                'notified_customer' => true,
            ]);
        });
    }
}
