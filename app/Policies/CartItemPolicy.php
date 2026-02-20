<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\User;

final class CartItemPolicy
{
    public function update(User $user, CartItem $cartItem): bool
    {
        return Cart::where('id', $cartItem->cart_id)->where('user_id', $user->id)->exists();
    }

    public function delete(User $user, CartItem $cartItem): bool
    {
        return Cart::where('id', $cartItem->cart_id)->where('user_id', $user->id)->exists();
    }
}
