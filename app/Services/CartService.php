<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final readonly  class CartService
{
    public function getOrCreateCart(?User $user, ?string $sessionId): Cart
    {
        if ($user) {
            return Cart::firstOrCreate(
                ['user_id' => $user->id],
                ['expires_at' => now()->addDays(30)]
            );
        }
        return Cart::firstOrCreate(
            ['session_id' => $sessionId],
            ['expires_at' => now()->addDays(7)]
        );
    }

    public function addItem(
        Cart            $cart,
        Product         $product,
        ?ProductVariant $variant,
        int             $quantity
    ): CartItem
    {
        $price = $variant ? $variant->price : $product->price;
        return CartItem::updateOrCreate(
            [
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'product_variant_id' => $variant?->id,
            ],
            [
                'quantity' => DB::raw("quantity + {$quantity}"),
                'price' => $price,
            ]
        );
    }

    public function updateQuantity(CartItem $item, int $quantity): bool
    {
        if ($quantity <= 0) {
            return $item->delete();
        }

        return $item->update(['quantity' => $quantity]);
    }

    public function removeItem(CartItem $item): bool
    {
        return $item->delete();
    }

    public function clearCart(Cart $cart): void
    {
        $cart->items()->delete();
    }

    public function calculateTotals(Cart $cart): array
    {
        $items = $cart->items()->with(['product', 'variant'])->get();

        $subtotal = $items->sum('subtotal');
        $itemCount = $items->sum('quantity');

        return [
            'subtotal' => $subtotal,
            'item_count' => $itemCount,
            'items' => $items,
        ];
    }
    public function mergeGuestCart(Cart $guestCart, Cart $userCart): void
    {
        DB::transaction(function () use ($guestCart, $userCart) {
            foreach ($guestCart->items as $guestItem) {
                $this->addItem(
                    $userCart,
                    $guestItem->product,
                    $guestItem->variant,
                    $guestItem->quantity
                );
            }

            $guestCart->delete();
        });
    }
}
