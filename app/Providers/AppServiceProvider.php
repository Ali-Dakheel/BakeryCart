<?php

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
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(Address::class, AddressPolicy::class);
        Gate::policy(Review::class, ReviewPolicy::class);
        Gate::policy(CartItem::class, CartItemPolicy::class);
        Gate::policy(Wishlist::class, WishlistPolicy::class);
    }
}
