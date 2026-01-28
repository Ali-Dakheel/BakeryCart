<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Resources\WishlistResource;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class WishlistController extends Controller
{
    public function index(): JsonResponse
    {
        $wishlist = Auth::user()->wishlist
            ->with(['product.translations', 'product.images'])
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'wishlist' => WishlistResource::collection($wishlist),
            'count' => $wishlist->count(),
        ], 200);
    }

    public function toggle(Product $product): JsonResponse
    {
        $user = Auth::user();

        $wishlistItem = Wishlist::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($wishlistItem) {
            $wishlistItem->delete();

            return response()->json([
                'message' => 'Product removed from wishlist',
                'in_wishlist' => false,
            ], 200);
        }

        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'added_at' => now(),
        ]);

        return response()->json([
            'message' => 'Product added to wishlist',
            'in_wishlist' => true,
        ], 200);
    }
}
