<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Payment> */
final class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'user_id' => User::factory(),
            'payment_method' => fake()->randomElement(['card', 'benefit_pay', 'cash_on_delivery']),
            'amount' => fake()->randomFloat(3, 5.000, 100.000),
            'currency' => 'BHD',
            'status' => 'pending',
            'gateway' => fake()->randomElement(['stripe', 'benefit', 'mock']),
            'transaction_id' => null,
            'gateway_response' => null,
            'gateway_data' => null,
            'is_mock' => true,
            'paid_at' => null,
            'failed_at' => null,
            'refunded_at' => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'transaction_id' => 'TXN-' . fake()->unique()->numerify('##########'),
            'paid_at' => now(),
            'gateway_response' => 'Payment successful',
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'failed_at' => now(),
            'gateway_response' => 'Insufficient funds',
        ]);
    }

    public function refunded(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'refunded',
            'transaction_id' => 'TXN-' . fake()->unique()->numerify('##########'),
            'paid_at' => now()->subDays(3),
            'refunded_at' => now(),
        ]);
    }

    public function card(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'card',
            'gateway' => 'stripe',
        ]);
    }

    public function benefitPay(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'benefit_pay',
            'gateway' => 'benefit',
        ]);
    }

    public function cashOnDelivery(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'cash_on_delivery',
            'gateway' => 'mock',
            'is_mock' => true,
        ]);
    }
}
