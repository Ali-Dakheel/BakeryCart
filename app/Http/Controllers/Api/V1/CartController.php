<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\AddItemRequest;
use App\Http\Requests\Cart\UpdateItemRequest;
use App\Http\Resources\CartItemResource;
use App\Http\Resources\CartResource;
use App\Http\Traits\ApiResponse;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

final class CartController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly CartService $cartService
    ) {
    }

    public function show(): JsonResponse
    {
        $user = Auth::user();
        $cartToken = $user ? null : request()->header('X-Cart-Token');
        $cart = $this->cartService->getOrCreateCart($user, $cartToken);

        $totals = $this->cartService->calculateTotals($cart);

        $cart->totals = $totals;
        $cart->load(['items.product.translations', 'items.product.images', 'items.variant']);

        return $this->success([
            'cart' => new CartResource($cart),
            'cart_token' => $user ? null : $cart->session_id, // Server token for guests only
        ]);
    }

    public function addItem(AddItemRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = Auth::user();
        $cartToken = $user ? null : request()->header('X-Cart-Token');
        $cart = $this->cartService->getOrCreateCart($user, $cartToken);
        $product = Product::findOrFail($validated['product_id']);

        $variant = isset($validated['product_variant_id'])
            ? ProductVariant::findOrFail($validated['product_variant_id'])
            : null;

        $cartItem = $this->cartService->addItem($cart, $product, $variant, $validated['quantity']);
        $cartItem->load(['product.translations', 'product.images', 'variant']);

        return $this->created([
            'item' => new CartItemResource($cartItem),
            'cart_token' => $user ? null : $cart->session_id, // Server token for guests only
        ], 'Item added to cart');
    }

    public function updateItem(UpdateItemRequest $request, CartItem $cartItem): JsonResponse
    {
        $user = Auth::user();
        $cartToken = $user ? null : request()->header('X-Cart-Token');

        if (!$this->canAccessCartItem($cartItem, $user, $cartToken)) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validated();
        $this->cartService->updateQuantity($cartItem, $validated['quantity']);

        $cartItem->load(['product.translations', 'product.images', 'variant']);

        return $this->success([
            'item' => new CartItemResource($cartItem),
        ], 'Cart updated');
    }

    public function removeItem(CartItem $cartItem): JsonResponse
    {
        $user = Auth::user();
        $cartToken = $user ? null : request()->header('X-Cart-Token');

        if (!$this->canAccessCartItem($cartItem, $user, $cartToken)) {
            abort(403, 'Unauthorized');
        }

        $this->cartService->removeItem($cartItem);

        return $this->success(null, 'Item removed from cart', 204);
    }

    public function clear(): JsonResponse
    {
        $user = Auth::user();
        $cartToken = $user ? null : request()->header('X-Cart-Token');
        $cart = $this->cartService->getOrCreateCart($user, $cartToken);
        $this->cartService->clearCart($cart);

        return $this->success(null, 'Cart cleared', 204);
    }

    private function canAccessCartItem(CartItem $cartItem, $user, ?string $cartToken): bool
    {
        $cart = $cartItem->cart;

        if ($user && $cart->user_id === $user->id) {
            return true;
        }

        if (!$user && $cart->session_id === $cartToken) {
            return true;
        }

        return false;
    }
}
