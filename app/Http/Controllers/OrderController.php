<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CancelOrderRequest;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService)
    {
    }

    public function index(): JsonResponse
    {
        $orders = Auth::user()->orders
            ->with('items')
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json([
            'orders' => OrderResource::collection($orders),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        ], 200);
    }

    public function store(CreateOrderRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = Auth::user();
        $data = [
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => $user->phone,
            ...$validated,
        ];

        $order = $this->orderService->createFromCart($user, $data);

        return response()->json([
            'message' => 'Order created successfully',
            'order' => new OrderResource($order),
        ], 201);
    }

    public function show(Order $order): JsonResponse
    {
        if ($order->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $order->load(['items', 'statusHistories']);

        return response()->json([
            'order' => new OrderResource($order)
        ], 200);
    }

    public function cancel(CancelOrderRequest $request, Order $order): JsonResponse
    {
        if ($order->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$order->is_cancelable) {
            return response()->json([
                'message' => 'This order cannot be cancelled'
            ], 400);
        }

        $validated = $request->validated();
        $this->orderService->cancel($order, $validated['reason'], 'customer');

        $order->refresh()->load(['items']);
        return response()->json([
            'message' => 'Order cancelled successfully',
            'order' => new OrderResource($order),
        ], 200);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): JsonResponse
    {
        $validated = $request->validated();
        $user = Auth::user();
        $this->orderService->updateStatus(
            $order,
            $validated['status'],
            $user,
            $validated['notes'] ?? null
        );
        $order->refresh()->load(['items', 'statusHistories']);

        return response()->json([
            'message' => 'Order status updated',
            'order' => new OrderResource($order),
        ], 200);
    }

}
