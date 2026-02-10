<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final readonly  class CartService
{
    /**
     * Generate cryptographically secure cart token
     * 256-bit entropy (Copenhagen Book recommendation: 120-256 bits)
     * 64 chars = 384 bits in base62 encoding
     */
    private function generateSecureCartToken(): string
    {
        return Str::random(64);
    }

    public function getOrCreateCart(?User $user, ?string $cartToken): Cart
    {
        $userCart = null;
        $guestCart = null;

        // Find existing carts
        if ($user) {
            $userCart = Cart::where('user_id', $user->id)->first();
        }

        if ($cartToken) {
            $guestCart = Cart::where('session_id', $cartToken)
                            ->whereNull('user_id')
                            ->first();
        }

        // Case 1: Both carts exist and are different → Merge
        if ($userCart && $guestCart && $userCart->id !== $guestCart->id) {
            $this->mergeGuestCart($guestCart, $userCart);
            return $userCart;
        }

        // Case 2: Only user cart exists
        if ($userCart) {
            return $userCart;
        }

        // Case 3: Only guest cart exists - associate with user if logged in
        if ($guestCart) {
            if ($user) {
                $guestCart->update(['user_id' => $user->id]);
            }
            return $guestCart;
        }

        // Case 4: No cart exists - create new
        return Cart::create([
            'user_id' => $user?->id,
            'session_id' => $cartToken ?? $this->generateSecureCartToken(),
            'expires_at' => now()->addDays($user ? 30 : 7),
        ]);
    }

    public function addItem(
        Cart            $cart,
        Product         $product,
        ?ProductVariant $variant,
        int             $quantity
    ): CartItem
    {
        $price = $variant ? $variant->price : $product->price;

        $cartItem = CartItem::firstOrNew([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'product_variant_id' => $variant?->id,
        ]);

        if ($cartItem->exists) {
            $cartItem->quantity += $quantity;
        } else {
            $cartItem->quantity = $quantity;
        }

        $cartItem->price = $price;
        $cartItem->save();

        return $cartItem;
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
                $existingItem = $userCart->items()
                    ->where('product_id', $guestItem->product_id)
                    ->where('product_variant_id', $guestItem->product_variant_id)
                    ->first();

                if ($existingItem) {
                    // Add quantities together for duplicates
                    $existingItem->update([
                        'quantity' => $existingItem->quantity + $guestItem->quantity
                    ]);
                } else {
                    // Move item to user cart
                    $guestItem->update(['cart_id' => $userCart->id]);
                }
            }

            // Delete empty guest cart
            $guestCart->delete();
        });
    }
}
