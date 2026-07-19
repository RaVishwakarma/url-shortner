<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! auth()->check()) {
            return response('Unauthenticated.', 401);
        }

        if (! in_array(auth()->user()->role, $roles, true)) {
            return response('Forbidden.', 403);
        }

        return $next($request);
    }
}
