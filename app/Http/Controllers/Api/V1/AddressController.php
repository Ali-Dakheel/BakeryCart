<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Http\Resources\AddressResource;
use App\Http\Traits\ApiResponse;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

final class AddressController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $addresses = Auth::user()->addresses()
            ->orderByDesc('is_default')
            ->orderByDesc('created_at')
            ->get();

        return $this->success([
            'addresses' => AddressResource::collection($addresses),
        ]);
    }

    public function store(StoreAddressRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = Auth::user();

        $address = $user->addresses()->create($validated);
        if ($validated['is_default'] ?? false) {
            $address->setAsDefault();
        }

        return $this->created([
            'address' => new AddressResource($address),
        ], 'Address created successfully');
    }

    public function show(Address $address): JsonResponse
    {
        $this->authorize('view', $address);

        return $this->success([
            'address' => new AddressResource($address),
        ]);
    }

    public function update(UpdateAddressRequest $request, Address $address): JsonResponse
    {
        $this->authorize('update', $address);

        $validated = $request->validated();
        $address->update($validated);

        if (isset($validated['is_default']) && $validated['is_default']) {
            $address->setAsDefault();
        }

        return $this->success([
            'address' => new AddressResource($address),
        ], 'Address updated successfully');
    }

    public function destroy(Address $address): JsonResponse
    {
        $this->authorize('delete', $address);

        if ($address->is_default) {
            return $this->error('Cannot delete default address. Please set another address as default first.', 400);
        }

        $address->delete();

        return $this->success(null, 'Address deleted successfully', 204);
    }

    public function setDefault(Address $address): JsonResponse
    {
        $this->authorize('setDefault', $address);

        $address->setAsDefault();

        return $this->success([
            'address' => new AddressResource($address),
        ], 'Default address updated');
    }
}
