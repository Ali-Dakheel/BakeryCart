<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Http\Traits\ApiResponse;
use App\Models\Cart;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly CartService $cartService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
        ]);
        $user->assignRole('customer');

        // Log user in (sets session cookie - secure, HttpOnly)
        Auth::login($user);

        // Merge guest cart if exists
        $guestCartToken = $request->header('X-Cart-Token');
        if ($guestCartToken) {
            $this->mergeGuestCartOnAuth($user, $guestCartToken);
        }

        return $this->created([
            'user' => new UserResource($user),
            'cart_token' => null, // Session-based, no token needed
        ], 'Registration successful');
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        // Auth::attempt logs user in and sets session cookie
        if (!Auth::attempt($credentials)) {
            return $this->error('Invalid credentials', 401);
        }

        // Merge guest cart if exists
        $guestCartToken = $request->header('X-Cart-Token');
        if ($guestCartToken) {
            $this->mergeGuestCartOnAuth(Auth::user(), $guestCartToken);
        }

        return $this->success([
            'user' => new UserResource(Auth::user()),
            'cart_token' => null, // Session-based, no token needed
        ], 'Login successful');
    }

    public function logout(): JsonResponse
    {
        Auth::logout();

        // Invalidate session for security
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return $this->success(null, 'Logged out successfully', 204);
    }

    public function user(): JsonResponse
    {
        return $this->success([
            'user' => new UserResource(Auth::user()),
        ]);
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = Auth::user();
        $validated = $request->validated();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return $this->error('Current password is incorrect', 400);
        }

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        // Invalidate all other sessions (Copenhagen Book security best practice)
        DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', session()->getId())
            ->delete();

        return $this->success(null, 'Password changed successfully. Other devices have been logged out.');
    }

    /**
     * Merge guest cart into user cart after authentication
     */
    private function mergeGuestCartOnAuth(User $user, string $guestCartToken): void
    {
        $guestCart = Cart::where('session_id', $guestCartToken)->first();

        if ($guestCart && $guestCart->items()->count() > 0) {
            $userCart = $this->cartService->getOrCreateCart($user, null);
            $this->cartService->mergeGuestCart($guestCart, $userCart);
        }
    }
}
