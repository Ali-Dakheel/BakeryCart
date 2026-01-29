<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Order $order): bool
    {
        return $user->id === $order->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function cancel(User $user, Order $order): bool
    {
        return $user->id === $order->user_id && $order->is_cancelable;
    }

    public function update(User $user, Order $order): bool
    {
        return $user->hasRole('admin');
    }

    public function updateStatus(User $user, Order $order): bool
    {
        return $user->hasRole('admin');
    }
}
