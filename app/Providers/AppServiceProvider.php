<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Address;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Review;
use App\Models\Wishlist;
use App\Policies\AddressPolicy;
use App\Policies\CartItemPolicy;
use App\Policies\OrderPolicy;
use App\Policies\ReviewPolicy;
use App\Policies\WishlistPolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Model::shouldBeStrict(! app()->isProduction());

        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(Address::class, AddressPolicy::class);
        Gate::policy(Review::class, ReviewPolicy::class);
        Gate::policy(CartItem::class, CartItemPolicy::class);
        Gate::policy(Wishlist::class, WishlistPolicy::class);
    }
}
