<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CancelOrderRequest;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Http\Traits\ApiResponse;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

final class OrderController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly OrderService $orderService)
    {
    }

    public function index(): JsonResponse
    {
        $orders = Auth::user()->orders()
            ->with('items')
            ->orderByDesc('created_at')
            ->paginate(15);

        return $this->paginated($orders, OrderResource::class, 'orders');
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

        return $this->created([
            'order' => new OrderResource($order),
        ], 'Order created successfully');
    }

    public function show(Order $order): JsonResponse
    {
        $this->authorize('view', $order);

        $order->load(['items', 'statusHistories']);

        return $this->success([
            'order' => new OrderResource($order),
        ]);
    }

    public function cancel(CancelOrderRequest $request, Order $order): JsonResponse
    {
        $this->authorize('view', $order);

        if (!$order->is_cancelable) {
            return $this->error('This order cannot be cancelled', 400);
        }

        $validated = $request->validated();
        $this->orderService->cancel($order, $validated['reason'], 'customer');

        $order->refresh()->load(['items']);

        return $this->success([
            'order' => new OrderResource($order),
        ], 'Order cancelled successfully');
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): JsonResponse
    {
        $this->authorize('updateStatus', $order);

        $validated = $request->validated();
        $user = Auth::user();
        $this->orderService->updateStatus(
            $order,
            $validated['status'],
            $user,
            $validated['notes'] ?? null
        );
        $order->refresh()->load(['items', 'statusHistories']);

        return $this->success([
            'order' => new OrderResource($order),
        ], 'Order status updated');
    }
}
