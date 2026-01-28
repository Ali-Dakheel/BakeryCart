<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class ReviewController extends Controller
{
    public function index(Product $product) :JsonResponse
    {
        $reviews = $product->approvedReviews()
            ->with('user')
            ->paginate(20);

        return response()->json([
            'reviews' => ReviewResource::collection($reviews),
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
            ],
            'product_stats' => [
                'average_rating' => round($product->average_rating, 1),
                'reviews_count' => $product->reviews_count,
            ],
        ], 200);
    }

    public function store(StoreReviewRequest $request, Product $product): JsonResponse
    {
        $user = Auth::user();
        $validated = $request->validated();

        $existingReview = Review::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'message' => 'You have already reviewed this product'
            ], 400);
        }

        $isVerifiedPurchase = false;
        $orderId = null;

        if (isset($validated['order_id'])) {
            $order = $user->orders()
                ->where('id', $validated['order_id'])
                ->where('status', 'delivered')
                ->whereHas('items', function ($query) use ($product) {
                    $query->where('product_id', $product->id);
                })
                ->first();

            if ($order) {
                $isVerifiedPurchase = true;
                $orderId = $order->id;
            }
        }

        $review = Review::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'order_id' => $orderId,
            'rating' => $validated['rating'],
            'title' => $validated['title'] ?? null,
            'comment' => $validated['comment'] ?? null,
            'is_verified_purchase' => $isVerifiedPurchase,
            'is_approved' => true,
        ]);

        $review->load(['user']);

        return response()->json([
            'message' => 'Review submitted successfully',
            'review' => new ReviewResource($review),
        ], 201);
    }

    public function markHelpful(Review $review): JsonResponse
    {
        $review->increment('helpful_count');

        return response()->json([
            'message' => 'Marked as helpful',
            'helpful_count' => $review->helpful_count,
        ], 200);
    }

    public function destroy(Review $review): JsonResponse
    {
        if ($review->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $review->delete();

        return response()->json(null, 204);
    }


}
