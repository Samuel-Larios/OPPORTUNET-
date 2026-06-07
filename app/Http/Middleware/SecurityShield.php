<?php

namespace App\Http\Middleware;

use App\Support\SecurityMonitor;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityShield
{
    public function handle(Request $request, Closure $next): Response
    {
        SecurityMonitor::ensureRequestAllowed($request);

        return $next($request);
    }
}
