<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        // Check if the user is logged in and has the 'super_admin' role
        if (!Auth::check() || Auth::user()->role !== 'super_admin') {
            // Redirect to a 403 Forbidden page or any other page you choose
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
