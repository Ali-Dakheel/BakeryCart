<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Http\Traits\ApiResponse;
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

        // Trigger cart merging and get the merged cart
        $cartToken = request()->cookie('cart_token');
        $cart = $this->cartService->getOrCreateCart($user, $cartToken);

        $response = $this->created(
            ['user' => new UserResource($user)],
            'Registration successful'
        );

        // Update cart token cookie if needed (cart may have new session_id after merge)
        if ($cart->session_id && $cart->session_id !== $cartToken) {
            $response->cookie(
                'cart_token',
                $cart->session_id,
                43200, // 30 days
                '/',
                config('session.domain'),
                config('session.secure'),
                true, // httpOnly
                false,
                config('session.same_site')
            );
        }

        return $response;
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials)) {
            return $this->error('Invalid credentials', 401);
        }

        /** @var User $user */
        $user = Auth::user();

        $cartToken = request()->cookie('cart_token');
        $cart = $this->cartService->getOrCreateCart($user, $cartToken);

        $response = $this->success(
            ['user' => new UserResource($user)],
            'Login successful'
        );

        // Update cart token cookie if needed (cart may have new session_id after merge)
        if ($cart->session_id && $cart->session_id !== $cartToken) {
            $response->cookie(
                'cart_token',
                $cart->session_id,
                43200, // 30 days
                '/',
                config('session.domain'),
                config('session.secure'),
                true, // httpOnly
                false,
                config('session.same_site')
            );
        }

        return $response;
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
        /** @var User $user */
        $user = Auth::user();

        return $this->success([
            'user' => new UserResource($user),
        ]);
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $validated = $request->validated();

        if (! Hash::check($validated['current_password'], $user->password)) {
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
}
