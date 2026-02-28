<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Coupon;
use App\Models\DeliveryZoneArea;
use App\Models\Order;
use App\Models\OrderCancellation;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final readonly class OrderService
{
    public function __construct(
        private ProductService $productService,
        private CartService $cartService
    ) {}

    public function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-'.date('Y').'-'.str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }

    public function createFromCart(?User $user, array $data): Order
    {
        return DB::transaction(function () use ($user, $data) {
            $cart = $this->cartService->getOrCreateCart($user, $data['session_id'] ?? null);
            $totals = $this->cartService->calculateTotals($cart);

            if ($totals['item_count'] === 0) {
                throw new \RuntimeException('Cart is empty');
            }

            foreach ($totals['items'] as $item) {
                $purchasable = $item->variant ?? $item->product;

                if (! $this->productService->isAvailableForPurchase($purchasable, $item->quantity)) {
                    throw new \RuntimeException("Product {$item->product->name} is not available");
                }
            }

            $couponDiscount = 0;
            $couponCode = null;

            if (! empty($data['coupon_code'])) {
                $coupon = $this->validateCoupon($data['coupon_code'], $user);
                $couponDiscount = $this->calculateCouponDiscount($coupon, $totals['subtotal']);
                $couponCode = $coupon->code;
            }

            $deliveryType = $data['delivery_type'] ?? 'delivery';
            $taxRate = $this->getTaxRate();
            $taxAmount = ($totals['subtotal'] - $couponDiscount) * ($taxRate / 100);
            $shippingFee = $this->calculateShipping($data['shipping_area'] ?? null, $totals['subtotal'], $deliveryType);
            $total = $totals['subtotal'] - $couponDiscount + $taxAmount + $shippingFee;

            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'user_id' => $user?->id,
                'delivery_type' => $deliveryType,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'] ?? null,
                'shipping_name' => $data['shipping_name'] ?? null,
                'shipping_phone' => $data['shipping_phone'] ?? null,
                'shipping_address_line_1' => $data['shipping_address_line_1'] ?? null,
                'shipping_address_line_2' => $data['shipping_address_line_2'] ?? null,
                'shipping_building' => $data['shipping_building'] ?? null,
                'shipping_floor' => $data['shipping_floor'] ?? null,
                'shipping_apartment' => $data['shipping_apartment'] ?? null,
                'shipping_area' => $data['shipping_area'] ?? null,
                'shipping_city' => $data['shipping_city'] ?? null,
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

            foreach ($totals['items'] as $cartItem) {
                $product = $cartItem->product;
                $variant = $cartItem->variant;
                $itemTax = ($cartItem->price * $cartItem->quantity) * ($taxRate / 100);

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
                    'tax_amount' => $itemTax,
                    'total' => $cartItem->subtotal + $itemTax,
                ]);

                if ($variant) {
                    $variant->decrementStock($cartItem->quantity);
                } else {
                    $this->productService->decrementStock($product, $cartItem->quantity);
                }

                $this->productService->incrementSales($product, $cartItem->quantity);
            }

            $this->addStatusHistory($order, 'pending', 'Order placed');

            if ($couponCode) {
                Coupon::where('code', $couponCode)->increment('used_count');
            }

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
            $attributes = ['status' => $newStatus];

            if ($newStatus === 'delivered') {
                $attributes['delivered_at'] = now();
                $attributes['fulfillment_status'] = 'fulfilled';
            }

            $order->update($attributes);
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
            'notified_customer' => false,
            'created_at' => now(),
        ]);
    }

    public function cancel(Order $order, string $reason, string $cancelledBy = 'customer'): bool
    {
        if (! $order->is_cancelable) {
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

            $cancellation->loadMissing('order');
            $cancellation->order->update(['payment_status' => 'refunded']);

            return true;
        });
    }

    private function validateCoupon(string $code, ?User $user): Coupon
    {
        $coupon = Coupon::where('code', $code)->valid()->firstOrFail();

        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            throw new \RuntimeException('Coupon usage limit reached');
        }

        if ($user && $coupon->usage_limit_per_user) {
            $userUsageCount = Order::where('user_id', $user->id)
                ->where('coupon_code', $code)
                ->count();

            if ($userUsageCount >= $coupon->usage_limit_per_user) {
                throw new \RuntimeException('You have already used this coupon');
            }
        }

        return $coupon;
    }

    private function calculateCouponDiscount(Coupon $coupon, float $subtotal): float
    {
        if ($coupon->min_order_amount && $subtotal < $coupon->min_order_amount) {
            throw new \RuntimeException('Minimum order amount is '.(string) $coupon->min_order_amount.' BHD');
        }

        $discount = match ($coupon->type) {
            'percentage' => $subtotal * ($coupon->value / 100),
            'fixed_amount' => (float) $coupon->value,
            default => 0.0,
        };

        if ($coupon->type === 'percentage' && $coupon->max_discount_amount) {
            $discount = min($discount, $coupon->max_discount_amount);
        }

        return $discount;
    }

    private function getTaxRate(): float
    {
        return 10.0;
    }

    private function calculateShipping(?string $area, float $cartTotal, string $deliveryType): float
    {
        if ($deliveryType === 'pickup') {
            return 0.000;
        }

        if ($area === null) {
            return 1.000;
        }

        $zoneArea = DeliveryZoneArea::whereRaw('LOWER(area_name) = LOWER(?)', [$area])->first();

        if (! $zoneArea) {
            return 1.000;
        }

        $zone = $zoneArea->deliveryZone;

        return ($zone?->is_active) ? $zone->calculateFee($cartTotal) : 1.000;
    }
}
