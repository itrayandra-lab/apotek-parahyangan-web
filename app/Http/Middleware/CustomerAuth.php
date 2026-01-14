<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CustomerAuth
{
    /**
     * Handle an incoming request.
     * Ensures the user is authenticated and has 'user' role.
     */
    public function handle(Request $request, Closure $next): Response
    {
        Auth::shouldUse('web');

        if (! Auth::guard('web')->check()) {
            return redirect()->route('login');
        }

        // Validate customer role
        if (Auth::guard('web')->user()->role !== 'user') {
            Auth::guard('web')->logout();

            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }

        return $next($request);
    }
}
