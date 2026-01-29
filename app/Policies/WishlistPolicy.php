<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\Wishlist;

class WishlistPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function toggle(User $user): bool
    {
        return true;
    }
}
