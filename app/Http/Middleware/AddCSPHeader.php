<?php

namespace App\Http\Middleware;

use Closure;

class AddCSPHeader
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $response->headers->set('Content-Security-Policy', "connect-src 'self' http://127.0.0.1:8001");
        return $response;
    }
}
