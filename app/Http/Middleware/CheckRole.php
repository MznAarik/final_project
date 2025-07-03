<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        // If the user is not authenticated
        if (!Auth::check()) {
            return redirect()->route('home')->with('error', 'Please login to access this page.');
        }

        $user = Auth::user();

        // If the user's role does not match
        if ($user->role !== $role) {
            return redirect()->route('home')->with('error', 'Unauthorized access.');
        }

        return $next($request);
    }
}
