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
        if (!Auth::check()) {
            return redirect()->route('home')->with(['status' => 3, 'message' => 'Please login to continue!']);
        }

        $user = Auth::user();

        if ($user->role !== 'admin') {
            return redirect()->route('home')->with(['status' => 3, 'message' => 'Unauthorized access!!!']);
        }

        return $next($request);
    }
}
