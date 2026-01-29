<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::any('{any}', function (string $any) {
    return redirect()->to('/api/v1/' . $any, 301);
})->where('any', '.*');
