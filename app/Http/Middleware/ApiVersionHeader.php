<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiVersionHeader
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-API-Version', 'v1');
        $response->headers->set('X-RateLimit-Limit', (string) config('app.rate_limit', 60));

        return $response;
    }
}
