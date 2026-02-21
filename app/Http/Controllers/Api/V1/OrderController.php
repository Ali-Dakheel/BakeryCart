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
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

final class OrderController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly OrderService $orderService) {}

    public function index(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $orders = $user->orders()
            ->with('items')
            ->orderByDesc('created_at')
            ->paginate(15);

        return $this->paginated($orders, OrderResource::class, 'orders');
    }

    public function store(CreateOrderRequest $request): JsonResponse
    {
        $validated = $request->validated();
        /** @var User $user */
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
        $this->authorize('cancel', $order);

        $validated = $request->validated();
        $order->load(['items.product', 'items.variant']);
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
        /** @var User $user */
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
