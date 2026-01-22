<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderCancellation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<OrderCancellation> */
final class OrderCancellationFactory extends Factory
{
    protected $model = OrderCancellation::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'cancelled_by' => User::factory(),
            'cancellation_reason' => fake()->randomElement([
                'Customer requested cancellation',
                'Out of stock',
                'Payment failed',
                'Delivery address unavailable',
                'Changed my mind',
                'Found better price elsewhere',
                'Order placed by mistake',
            ]),
            'refund_amount' => null,
            'refund_status' => null,
            'refund_method' => null,
            'refund_transaction_id' => null,
            'cancelled_at' => now(),
            'refunded_at' => null,
        ];
    }

    public function withRefund(float $amount = null): static
    {
        return $this->state(fn (array $attributes) => [
            'refund_amount' => $amount ?? fake()->randomFloat(3, 5.000, 50.000),
            'refund_status' => 'pending',
            'refund_method' => fake()->randomElement(['original_payment', 'store_credit', 'bank_transfer']),
        ]);
    }

    public function refundCompleted(): static
    {
        return $this->state(fn (array $attributes) => [
            'refund_amount' => $attributes['refund_amount'] ?? fake()->randomFloat(3, 5.000, 50.000),
            'refund_status' => 'completed',
            'refund_method' => fake()->randomElement(['original_payment', 'store_credit', 'bank_transfer']),
            'refund_transaction_id' => 'REF-' . fake()->unique()->numerify('##########'),
            'refunded_at' => now(),
        ]);
    }

    public function refundFailed(): static
    {
        return $this->state(fn (array $attributes) => [
            'refund_amount' => $attributes['refund_amount'] ?? fake()->randomFloat(3, 5.000, 50.000),
            'refund_status' => 'failed',
            'refund_method' => fake()->randomElement(['original_payment', 'store_credit', 'bank_transfer']),
        ]);
    }

    public function byCustomer(): static
    {
        return $this->state(fn (array $attributes) => [
            'cancellation_reason' => fake()->randomElement([
                'Changed my mind',
                'Found better price elsewhere',
                'Order placed by mistake',
                'No longer needed',
            ]),
        ]);
    }

    public function byAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'cancellation_reason' => fake()->randomElement([
                'Out of stock',
                'Payment verification failed',
                'Delivery address unavailable',
                'Suspected fraud',
            ]),
        ]);
    }
}
