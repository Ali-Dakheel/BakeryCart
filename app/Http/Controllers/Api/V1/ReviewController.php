<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Http\Traits\ApiResponse;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

final class ReviewController extends Controller
{
    use ApiResponse;

    public function index(Product $product): JsonResponse
    {
        $reviews = $product->approvedReviews()
            ->with('user')
            ->paginate(20);

        return $this->success([
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
        ]);
    }

    public function store(StoreReviewRequest $request, Product $product): JsonResponse
    {
        $user = Auth::user();
        $validated = $request->validated();

        $existingReview = Review::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existingReview) {
            return $this->error('You have already reviewed this product', 400);
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

        return $this->created([
            'review' => new ReviewResource($review),
        ], 'Review submitted successfully');
    }

    public function markHelpful(Review $review): JsonResponse
    {
        $this->authorize('markHelpful', $review);

        $review->increment('helpful_count');

        return $this->success([
            'helpful_count' => $review->helpful_count,
        ], 'Marked as helpful');
    }

    public function destroy(Review $review): JsonResponse
    {
        $this->authorize('delete', $review);

        $review->delete();

        return $this->success(null, 'Review deleted successfully', 204);
    }
}
