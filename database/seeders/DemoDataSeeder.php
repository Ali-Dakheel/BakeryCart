<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

final class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::role('customer')->get();
        $products = Product::with('translations')->get();

        if ($customers->isEmpty() || $products->isEmpty()) {
            $this->command->warn('No customers or products found. Run other seeders first.');
            return;
        }

        $this->seedAddresses($customers);
        $this->seedOrders($customers, $products);
        $this->seedReviews($customers, $products);

        $this->command->info('Demo data seeded: addresses, orders, payments, and reviews');
    }

    private function seedAddresses($customers): void
    {
        foreach ($customers as $customer) {
            Address::factory()
                ->count(fake()->numberBetween(1, 2))
                ->for($customer)
                ->create();

            $customer->addresses()->first()?->update(['is_default' => true]);
        }

        $this->command->info('  - Addresses created for ' . $customers->count() . ' customers');
    }

    private function seedOrders($customers, $products): void
    {
        $orderCount = 0;

        foreach ($customers->take(8) as $customer) {
            $numOrders = fake()->numberBetween(1, 3);

            for ($i = 0; $i < $numOrders; $i++) {
                $status = fake()->randomElement(['pending', 'confirmed', 'delivered', 'delivered', 'delivered']);

                $order = Order::factory()
                    ->for($customer)
                    ->create([
                        'status' => $status,
                        'payment_status' => $status === 'pending' ? 'pending' : 'paid',
                        'fulfillment_status' => $status === 'delivered' ? 'fulfilled' : 'unfulfilled',
                        'delivered_at' => $status === 'delivered' ? now()->subDays(fake()->numberBetween(1, 30)) : null,
                    ]);

                $orderProducts = $products->random(fake()->numberBetween(1, 4));
                $subtotal = 0;

                foreach ($orderProducts as $product) {
                    $quantity = fake()->numberBetween(1, 3);
                    $unitPrice = (float) $product->price;
                    $itemTotal = $unitPrice * $quantity;
                    $subtotal += $itemTotal;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->translations->where('locale', 'en')->first()?->name ?? 'Product',
                        'product_sku' => $product->sku,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'discount_amount' => 0,
                        'tax_amount' => round($itemTotal * 0.10, 3),
                        'total' => round($itemTotal * 1.10, 3),
                    ]);
                }

                $taxAmount = round($subtotal * 0.10, 3);
                $shippingFee = 1.000;
                $total = round($subtotal + $taxAmount + $shippingFee, 3);

                $order->update([
                    'subtotal' => $subtotal,
                    'tax_amount' => $taxAmount,
                    'shipping_fee' => $shippingFee,
                    'total' => $total,
                ]);

                if ($order->payment_status === 'paid') {
                    Payment::create([
                        'order_id' => $order->id,
                        'user_id' => $customer->id,
                        'payment_method' => fake()->randomElement(['card', 'benefit_pay']),
                        'amount' => $total,
                        'currency' => 'BHD',
                        'status' => 'completed',
                        'gateway' => fake()->randomElement(['stripe', 'benefit']),
                        'transaction_id' => 'TXN-' . fake()->numerify('##########'),
                        'is_mock' => true,
                        'paid_at' => $order->created_at,
                    ]);
                }

                $order->statusHistories()->create([
                    'status' => $order->status,
                    'notes' => 'Order ' . ($status === 'pending' ? 'placed' : $status),
                    'notified_customer' => true,
                    'created_at' => $order->created_at,
                ]);

                $orderCount++;
            }
        }

        $this->command->info("  - {$orderCount} orders with items and payments created");
    }

    private function seedReviews($customers, $products): void
    {
        $reviewCount = 0;

        $customersWithOrders = $customers->filter(function ($customer) {
            return $customer->orders()->where('status', 'delivered')->exists();
        });

        foreach ($customersWithOrders as $customer) {
            $deliveredOrders = $customer->orders()->where('status', 'delivered')->with('items')->get();

            foreach ($deliveredOrders as $order) {
                if (fake()->boolean(60)) {
                    foreach ($order->items->take(fake()->numberBetween(1, 2)) as $item) {
                        $existingReview = Review::where('user_id', $customer->id)
                            ->where('product_id', $item->product_id)
                            ->exists();

                        if ($existingReview) {
                            continue;
                        }

                        Review::create([
                            'product_id' => $item->product_id,
                            'user_id' => $customer->id,
                            'order_id' => $order->id,
                            'rating' => fake()->randomElement([4, 4, 5, 5, 5]),
                            'title' => fake()->optional(0.7)->sentence(4),
                            'comment' => fake()->paragraph(),
                            'is_verified_purchase' => true,
                            'is_approved' => fake()->boolean(90),
                            'helpful_count' => fake()->numberBetween(0, 20),
                        ]);

                        $reviewCount++;
                    }
                }
            }
        }

        $this->command->info("  - {$reviewCount} product reviews created");
    }
}
