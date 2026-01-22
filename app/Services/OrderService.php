<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderCancellation;
use App\Models\OrderStatusHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\OrderItem;

final readonly class OrderService
{
    public function __construct(
        private ProductService $productService,
        private CartService    $cartService
    )
    {
    }

    public function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-' . date('Y') . '-' . str_pad((string)rand(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }

    public function createFromCart(?User $user, array $data): Order
    {
        return DB::transaction(function () use ($user, $data) {
            $cart = $this->cartService->getOrCreateCart($user, $data['session_id'] ?? null);
            $totals = $this->cartService->calculateTotals($cart);

            if ($totals['item_count'] === 0) {
                throw new \Exception('Cart is empty');
            }

            // Validate stock availability
            foreach ($totals['items'] as $item) {
                $product = $item->variant ? $item->variant : $item->product;

                if (!$this->productService->isAvailableForPurchase($product, $item->quantity)) {
                    throw new \Exception("Product {$item->product->name} is not available");
                }
            }

            // Apply coupon if provided
            $couponDiscount = 0;
            $couponCode = null;
            if (!empty($data['coupon_code'])) {
                $coupon = $this->validateCoupon($data['coupon_code'], $user);
                $couponDiscount = $this->calculateCouponDiscount($coupon, $totals['subtotal']);
                $couponCode = $coupon->code;
            }

            // Calculate tax
            $taxRate = $this->getTaxRate();
            $taxableAmount = $totals['subtotal'] - $couponDiscount;
            $taxAmount = $taxableAmount * ($taxRate / 100);

            // Calculate shipping
            $shippingFee = $this->calculateShipping($data['shipping_area'] ?? null);

            // Calculate final total
            $total = $totals['subtotal'] - $couponDiscount + $taxAmount + $shippingFee;

            // Create order
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'user_id' => $user?->id,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'] ?? null,
                'shipping_name' => $data['shipping_name'],
                'shipping_phone' => $data['shipping_phone'],
                'shipping_address_line_1' => $data['shipping_address_line_1'],
                'shipping_address_line_2' => $data['shipping_address_line_2'] ?? null,
                'shipping_building' => $data['shipping_building'] ?? null,
                'shipping_floor' => $data['shipping_floor'] ?? null,
                'shipping_apartment' => $data['shipping_apartment'] ?? null,
                'shipping_area' => $data['shipping_area'],
                'shipping_city' => $data['shipping_city'] ?? 'Manama',
                'delivery_instructions' => $data['delivery_instructions'] ?? null,
                'subtotal' => $totals['subtotal'],
                'coupon_code' => $couponCode,
                'coupon_discount' => $couponDiscount,
                'tax_percentage' => $taxRate,
                'tax_amount' => $taxAmount,
                'shipping_fee' => $shippingFee,
                'total' => $total,
                'currency' => 'BHD',
                'status' => 'pending',
                'payment_status' => 'pending',
                'fulfillment_status' => 'unfulfilled',
                'customer_notes' => $data['customer_notes'] ?? null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'source' => $data['source'] ?? 'web',
            ]);

            // Create order items
            foreach ($totals['items'] as $cartItem) {
                $product = $cartItem->product;
                $variant = $cartItem->variant;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_variant_id' => $variant?->id,
                    'product_name' => $product->name,
                    'product_sku' => $variant?->sku ?? $product->sku,
                    'product_description' => $product->short_description,
                    'variant_name' => $variant?->name,
                    'variant_attributes' => $variant ? [
                        'pack_quantity' => $variant->pack_quantity,
                        'weight_grams' => $variant->weight_grams,
                    ] : null,
                    'image_url' => $product->images->first()?->image_url,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->price,
                    'tax_amount' => ($cartItem->price * $cartItem->quantity) * ($taxRate / 100),
                    'total' => $cartItem->subtotal + (($cartItem->price * $cartItem->quantity) * ($taxRate / 100)),
                ]);

                // Decrement stock
                if ($variant) {
                    $variant->decrementStock($cartItem->quantity);
                } else {
                    $this->productService->decrementStock($product, $cartItem->quantity);
                }

                // Increment sales count
                $this->productService->incrementSales($product, $cartItem->quantity);
            }

            // Create initial status history
            $this->addStatusHistory($order, 'pending', 'Order placed');

            // Increment coupon usage
            if ($couponCode) {
                Coupon::where('code', $couponCode)->increment('used_count');
            }

            // Clear cart
            $this->cartService->clearCart($cart);

            return $order->load(['items', 'statusHistories']);
        });
    }

    public function updateStatus(Order $order, string $newStatus, ?User $user = null, ?string $notes = null): bool
    {
        if ($order->status === $newStatus) {
            return false;
        }

        DB::transaction(function () use ($order, $newStatus, $user, $notes) {
            $order->update(['status' => $newStatus]);

            if ($newStatus === 'delivered') {
                $order->update([
                    'delivered_at' => now(),
                    'fulfillment_status' => 'fulfilled',
                ]);
            }

            $this->addStatusHistory($order, $newStatus, $notes, $user);
        });

        return true;
    }

    public function addStatusHistory(Order $order, string $status, ?string $notes = null, ?User $user = null): OrderStatusHistory
    {
        return OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => $status,
            'notes' => $notes,
            'changed_by' => $user?->id,
            'notified_customer' => false, // Set to true when email sent
            'created_at' => now(),
        ]);
    }

    public function cancel(Order $order, string $reason, string $cancelledBy = 'customer'): bool
    {
        if (!$order->is_cancelable) {
            return false;
        }

        return DB::transaction(function () use ($order, $reason, $cancelledBy) {
            $order->update([
                'status' => 'cancelled',
                'cancellation_reason' => $reason,
            ]);

            OrderCancellation::create([
                'order_id' => $order->id,
                'cancelled_by' => $cancelledBy,
                'cancellation_reason' => $reason,
                'refund_amount' => $order->total,
                'refund_status' => 'pending',
                'cancelled_at' => now(),
            ]);

            // Restore stock
            foreach ($order->items as $item) {
                if ($item->product_variant_id) {
                    $item->variant->incrementStock($item->quantity);
                } else {
                    $this->productService->incrementStock($item->product, $item->quantity);
                }
            }

            $this->addStatusHistory($order, 'cancelled', "Cancelled: {$reason}");

            return true;
        });
    }

    public function processRefund(OrderCancellation $cancellation, string $transactionId): bool
    {
        return DB::transaction(function () use ($cancellation, $transactionId) {
            $cancellation->update([
                'refund_status' => 'completed',
                'refund_transaction_id' => $transactionId,
                'refunded_at' => now(),
            ]);

            $cancellation->order->update([
                'payment_status' => 'refunded',
            ]);

            return true;
        });
    }

    private function validateCoupon(string $code, ?User $user): Coupon
    {
        $coupon = Coupon::where('code', $code)
            ->valid()
            ->firstOrFail();

        // Check usage limit
        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            throw new \Exception('Coupon usage limit reached');
        }

        // Check per-user limit
        if ($user && $coupon->usage_limit_per_user) {
            $userUsageCount = Order::where('user_id', $user->id)
                ->where('coupon_code', $code)
                ->count();

            if ($userUsageCount >= $coupon->usage_limit_per_user) {
                throw new \Exception('You have already used this coupon');
            }
        }

        return $coupon;
    }
    private function calculateCouponDiscount(Coupon $coupon, float $subtotal): float
    {
        // Check minimum order amount
        if ($coupon->min_order_amount && $subtotal < $coupon->min_order_amount) {
            throw new \Exception("Minimum order amount is {$coupon->min_order_amount} BHD");
        }

        $discount = match($coupon->type) {
            'percentage' => $subtotal * ($coupon->value / 100),
            'fixed_amount' => $coupon->value,
            'free_shipping' => 0, // Handled separately
            default => 0,
        };

        // Apply max discount cap for percentage coupons
        if ($coupon->type === 'percentage' && $coupon->max_discount_amount) {
            $discount = min($discount, $coupon->max_discount_amount);
        }

        return $discount;
    }
    private function getTaxRate(): float
    {
        // For now, return 10% VAT
        // TODO: Implement dynamic tax rate from TaxRate model
        return 10.0;
    }

    /**
     * Calculate shipping fee
     */
    private function calculateShipping(?string $area): float
    {
        // For now, flat rate
        // TODO: Implement delivery zone logic
        return 1.000;
    }
}
