<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Requests\Cart\AddItemRequest;
use App\Http\Requests\Cart\UpdateItemRequest;
use App\Http\Resources\CartItemResource;
use App\Http\Resources\CartResource;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cartService
    )
    {
    }

    public function show(): JsonResponse
    {
        $user = Auth::user();
        $cart = $this->cartService->getOrCreateCart($user, null);

        $totals = $this->cartService->calculateTotals($cart);

        $cart->totals = $totals;
        $cart->load(['items.product.translations', 'items.product.images', 'items.variant']);

        return response()->json([
            'cart' => new CartResource($cart),
        ], 200);
    }

    public function addItem(AddItemRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = Auth::user();
        $cart = $this->cartService->getOrCreateCart($user, null);
        $product = Product::findOrFail($validated['product_id']);

        $variant = isset($validated['product_variant_id'])
            ? ProductVariant::findOrFail($validated['product_variant_id'])
            : null;

        $cartItem = $this->cartService->addItem($cart, $product, $variant, $validated['quantity']);
        $cartItem->load(['product.translations', 'product.images', 'variant']);

        return response()->json([
            'message' => 'Item added to cart',
            'item' => new CartItemResource($cartItem)
        ], 201);
    }

    public function updateItem(UpdateItemRequest $request, CartItem $cartItem): JsonResponse
    {
        if ($cartItem->cart->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $validated = $request->validated();
        $this->cartService->updateQuantity($cartItem, $validated['quantity']);

        $cartItem->load(['product.translations', 'product.images', 'variant']);
        return response()->json([
            'message' => 'Cart updated',
            'item' => new CartItemResource($cartItem)
        ], 200);
    }

    public function removeItem(CartItem $cartItem): JsonResponse
    {
        if ($cartItem->cart->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $this->cartService->removeItem($cartItem);

        return response()->json(null, 204);
    }

    public function clear(): JsonResponse
    {
        $user = Auth::user();
        $cart = $this->cartService->getOrCreateCart($user, null);
        $this->cartService->clearCart($cart);
        return response()->json(null, 204);
    }

}
