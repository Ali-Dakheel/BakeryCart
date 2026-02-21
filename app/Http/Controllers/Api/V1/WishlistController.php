<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\WishlistResource;
use App\Http\Traits\ApiResponse;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

final class WishlistController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $wishlist = Auth::user()->wishlists()
            ->with(['product.translations', 'product.images'])
            ->orderByDesc('created_at')
            ->get();

        return $this->success([
            'wishlist' => WishlistResource::collection($wishlist),
            'count' => $wishlist->count(),
        ]);
    }

    public function toggle(Product $product): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $wishlistItem = Wishlist::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($wishlistItem) {
            $wishlistItem->delete();

            return $this->success([
                'in_wishlist' => false,
            ], 'Product removed from wishlist');
        }

        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'added_at' => now(),
        ]);

        return $this->success([
            'in_wishlist' => true,
        ], 'Product added to wishlist');
    }
}
