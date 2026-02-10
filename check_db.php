<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== USERS ===\n";
$users = DB::table('users')->get(['id', 'name', 'email']);
echo "Total users: " . $users->count() . "\n";
foreach ($users as $user) {
    echo "ID: {$user->id}, Name: {$user->name}, Email: {$user->email}\n";
}

echo "\n=== SESSIONS ===\n";
$sessions = DB::table('sessions')->orderBy('last_activity', 'desc')->limit(3)->get(['id', 'user_id', 'last_activity']);
echo "Total sessions: " . DB::table('sessions')->count() . "\n";
foreach ($sessions as $session) {
    $userId = $session->user_id ?? 'NULL';
    $time = date('Y-m-d H:i:s', $session->last_activity);
    echo "ID: {$session->id}, User ID: {$userId}, Last Activity: {$time}\n";
}

echo "\n=== CARTS ===\n";
$carts = DB::table('carts')->get(['id', 'user_id', 'session_id']);
echo "Total carts: " . $carts->count() . "\n";
foreach ($carts as $cart) {
    $userId = $cart->user_id ?? 'NULL';
    echo "ID: {$cart->id}, User ID: {$userId}, Session ID: " . substr($cart->session_id, 0, 20) . "...\n";
}

echo "\n=== CART ITEMS ===\n";
$cartItems = DB::table('cart_items')->get(['id', 'cart_id', 'product_id', 'product_variant_id', 'quantity']);
echo "Total cart items: " . $cartItems->count() . "\n";
foreach ($cartItems as $item) {
    $variantId = $item->product_variant_id ?? 'NULL';
    echo "Cart: {$item->cart_id}, Product: {$item->product_id}, Variant: {$variantId}, Qty: {$item->quantity}\n";
}
