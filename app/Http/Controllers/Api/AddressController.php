<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class AddressController extends Controller
{
    public function index(): JsonResponse
    {
        $addresses = Auth::user()->addresses
        ->orderByDesc('is_default')
        ->orderByDesc('created_at')
        ->get();

        return response()->json([
            'addresses' => AddressResource::collection($addresses)
        ], 200);
    }

    public function store(StoreAddressRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = Auth::user();

        $address = $user->addresses()->create($validated);
        if ($validated['is_default'] ?? false) {
            $address->setAsDefault();
        }
        return response()->json([
            'message' => 'Address created successfully',
            'address' => new AddressResource($address),
        ], 201);
    }

    public function show(Address $address): JsonResponse
    {
        if ($address->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'address' => new AddressResource($address)
        ], 200);
    }

    public function update(UpdateAddressRequest $request, Address $address): JsonResponse
    {
        if ($address->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validated();
        $address->update($validated);

        if (isset($validated['is_default']) && $validated['is_default']) {
            $address->setAsDefault();
        }

        return response()->json([
            'message' => 'Address updated successfully',
            'address' => new AddressResource($address),
        ], 200);
    }

    public function destroy(Address $address): JsonResponse
    {
        if ($address->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($address->is_default) {
            return response()->json([
                'message' => 'Cannot delete default address. Please set another address as default first.'
            ], 400);
        }

        $address->delete();

        return response()->json(null, 204);
    }

    public function setDefault(Address $address): JsonResponse
    {
        if ($address->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $address->setAsDefault();

        return response()->json([
            'message' => 'Default address updated',
            'address' => new AddressResource($address),
        ], 200);
    }
}
